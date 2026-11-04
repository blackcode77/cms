<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Application\Order\Exception\ManagerNotAssignableException;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\Role\Value\Permission;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserId;

/**
 * A Manager is any User whose Role holds {@see Permission::HandleOrders}.
 */
final readonly class ManagerPolicy
{
    public function __construct(
        private UserRepositoryInterface $users,
        private RoleRepositoryInterface $roles,
    ) {}

    /**
     * @throws ManagerNotAssignableException
     */
    public function assertAssignable(?UserId $managerId): void
    {
        if ($managerId === null) {
            return;
        }
        $user = $this->users->findById($managerId);
        $role = $user === null ? null : $this->roles->findById($user->roleId);
        if ($role === null || !$role->has(Permission::HandleOrders)) {
            throw ManagerNotAssignableException::create();
        }
    }
}
