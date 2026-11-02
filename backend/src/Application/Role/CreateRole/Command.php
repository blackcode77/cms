<?php

declare(strict_types=1);

namespace App\Application\Role\CreateRole;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleName;

final readonly class Command
{
    /**
     * @param list<Permission> $permissions
     */
    public function __construct(
        public RoleName $name,
        public array $permissions,
    ) {}
}
