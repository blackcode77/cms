<?php

declare(strict_types=1);

namespace App\Application\User\DeleteUser;

use App\Application\User\Exception\CannotDeleteYourselfException;
use App\Application\User\Exception\LastAdminException;
use App\Application\User\Exception\UserNotFoundException;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private RefreshTokenRepositoryInterface $refreshTokens,
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws CannotDeleteYourselfException
     * @throws LastAdminException
     */
    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById($command->id) ?? throw UserNotFoundException::create();
        if ($user->id->isEqualTo($command->actorId)) {
            throw CannotDeleteYourselfException::create();
        }

        $role = $this->roleRepository->findById($user->roleId);
        if ($role !== null && $role->isAdmin() && $this->userRepository->countByRole($role->id) <= 1) {
            throw LastAdminException::create();
        }

        $this->refreshTokens->revokeAllForUser($user->id);
        $this->userRepository->remove($user->id);
    }
}
