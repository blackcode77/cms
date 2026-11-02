<?php

declare(strict_types=1);

namespace App\Application\Auth\ChangePassword;

use App\Application\Auth\Exception\InvalidCurrentPasswordException;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * @throws InvalidCurrentPasswordException
     */
    public function handle(User $user, string $currentPassword, string $newPassword): void
    {
        if (!$this->passwordHasher->verify($currentPassword, $user->passwordHash)) {
            throw InvalidCurrentPasswordException::create();
        }

        $this->users->changePassword($user->id, $this->passwordHasher->hash($newPassword), false);
    }
}
