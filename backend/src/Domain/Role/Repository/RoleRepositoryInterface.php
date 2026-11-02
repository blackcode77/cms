<?php

declare(strict_types=1);

namespace App\Domain\Role\Repository;

use App\Domain\Role\Entity\Role;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;

interface RoleRepositoryInterface
{
    public function findById(RoleId $id): ?Role;

    public function findByName(RoleName $name): ?Role;

    public function add(Role $role): void;

    /**
     * Persists the name and Permissions of an existing Role.
     */
    public function save(Role $role): void;

    public function delete(RoleId $id): void;

    /**
     * Number of Users holding the Role.
     */
    public function countUsers(RoleId $id): int;
}
