<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Role;

use App\Domain\Role\Entity\Role;
use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use Codeception\Test\Unit;
use Ramsey\Uuid\Uuid;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class RoleTest extends Unit
{
    public function testPermissionCatalogIsFixed(): void
    {
        assertSame(
            [
                'view_dashboard', 'edit_page', 'view_users', 'manage_users', 'view_roles', 'manage_roles',
                'view_orders', 'manage_orders', 'handle_orders', 'view_clients', 'manage_clients',
            ],
            array_map(static fn(Permission $p) => $p->value, Permission::cases()),
        );
    }

    public function testAdminRoleHoldsAllPermissionsEvenWhenGivenNone(): void
    {
        $role = new Role(new RoleId(Uuid::uuid7()), new RoleName(RoleName::ADMIN), []);

        assertTrue($role->isAdmin());
        assertCount(count(Permission::cases()), $role->permissions);
        foreach (Permission::cases() as $permission) {
            assertTrue($role->has($permission));
        }
    }

    public function testOrdinaryRoleHoldsOnlyGrantedPermissionsWithoutDuplicates(): void
    {
        $role = new Role(
            new RoleId(Uuid::uuid7()),
            new RoleName(RoleName::MANAGER),
            [Permission::ViewDashboard, Permission::EditPage, Permission::EditPage],
        );

        assertFalse($role->isAdmin());
        assertCount(2, $role->permissions);
        assertTrue($role->has(Permission::EditPage));
        assertFalse($role->has(Permission::ManageUsers));
    }
}
