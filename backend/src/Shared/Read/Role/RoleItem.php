<?php

declare(strict_types=1);

namespace App\Shared\Read\Role;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;

final readonly class RoleItem
{
    /**
     * @param list<Permission> $permissions
     */
    public function __construct(
        public RoleId $id,
        public RoleName $name,
        public array $permissions,
        public int $usersCount,
    ) {}

    /**
     * Admin is read-only (always holds every Permission).
     */
    public function isReadOnly(): bool
    {
        return $this->name->toString() === RoleName::ADMIN;
    }

    public function isDeletable(): bool
    {
        return !in_array($this->name->toString(), [RoleName::ADMIN, RoleName::VISITER], true) && $this->usersCount === 0;
    }
}
