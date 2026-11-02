<?php

declare(strict_types=1);

namespace App\Application\Role\UpdateRole;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;

final readonly class Command
{
    /**
     * @param list<Permission> $permissions
     */
    public function __construct(
        public RoleId $id,
        public RoleName $name,
        public array $permissions,
    ) {}
}
