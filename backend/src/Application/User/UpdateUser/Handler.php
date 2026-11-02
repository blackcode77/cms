<?php

declare(strict_types=1);

namespace App\Application\User\UpdateUser;

use App\Application\User\AdminGuard;
use App\Application\User\Exception\AdminRequiredException;
use App\Application\User\Exception\LastAdminException;
use App\Application\User\Exception\RoleNotAssignableException;
use App\Application\User\Exception\UserEmailAlreadyExistsException;
use App\Application\User\Exception\UserNotFoundException;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private AdminGuard $adminGuard,
    ) {}

    /**
     * @throws UserNotFoundException
     * @throws RoleNotAssignableException
     * @throws UserEmailAlreadyExistsException
     * @throws LastAdminException
     * @throws AdminRequiredException
     */
    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById($command->id) ?? throw UserNotFoundException::create();

        $role = $this->roleRepository->findById($command->roleId);
        if ($role === null || $role->isVisiter()) {
            throw RoleNotAssignableException::create();
        }

        $this->adminGuard->assertMayManage($command->actorId, $user);
        $this->adminGuard->assertMayAssign($command->actorId, $role);

        $other = $this->userRepository->findByEmail($command->email);
        if ($other !== null && !$other->id->isEqualTo($user->id)) {
            throw UserEmailAlreadyExistsException::fromEmail($command->email);
        }

        if (!$role->id->isEqualTo($user->roleId)) {
            $current = $this->roleRepository->findById($user->roleId);
            if (
                $current !== null
                && $current->isAdmin()
                && $this->userRepository->countByRole($current->id) <= 1
            ) {
                throw LastAdminException::create();
            }
        }

        $user->edit($command->email, $command->name, $role->id);
        $this->userRepository->update($user);
    }
}
