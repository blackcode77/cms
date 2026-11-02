<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Role\Value\RoleId;
use App\Domain\User\Entity\User;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;

interface UserRepositoryInterface
{
    public function findByEmail(UserEmail $email): ?User;

    public function findById(UserId $id): ?User;

    public function hasByEmail(UserEmail $email): bool;

    public function add(User $user): void;

    /**
     * Persists the editable fields (email, name, Role) of an existing User.
     */
    public function update(User $user): void;

    public function remove(UserId $id): void;

    /**
     * Number of Users holding the Role.
     */
    public function countByRole(RoleId $roleId): int;
}
