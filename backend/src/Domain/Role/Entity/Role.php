<?php

declare(strict_types=1);

namespace App\Domain\Role\Entity;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;

final class Role
{
    public readonly RoleId $id;
    public private(set) RoleName $name;
    /** @var list<Permission> */
    public private(set) array $permissions;

    /**
     * @param list<Permission> $permissions ignored for the Admin Role, which always holds every Permission
     */
    public function __construct(RoleId $id, RoleName $name, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $name->toString() === RoleName::ADMIN
            ? Permission::cases()
            : array_values(array_unique($permissions, SORT_REGULAR));
    }

    public function isAdmin(): bool
    {
        return $this->name->toString() === RoleName::ADMIN;
    }

    /**
     * The Visiter Role is the implicit guest Role: it can never be assigned to a User.
     */
    public function isVisiter(): bool
    {
        return $this->name->toString() === RoleName::VISITER;
    }

    public function has(Permission $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
