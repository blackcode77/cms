<?php

declare(strict_types=1);

namespace App\Domain\Role\Repository;

use App\Domain\Role\Entity\Role;
use App\Domain\Role\Value\RoleName;

interface RoleRepositoryInterface
{
    public function findByName(RoleName $name): ?Role;

    public function add(Role $role): void;
}
