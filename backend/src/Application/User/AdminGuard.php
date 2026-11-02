<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Application\User\Exception\AdminRequiredException;
use App\Domain\Role\Entity\Role;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserId;

/**
 * Only an Admin may assign the Admin Role or edit / reset / delete a User who holds it
 * (otherwise `manage_users` alone would allow privilege escalation).
 */
final readonly class AdminGuard
{
    public function __construct(
        private UserRepositoryInterface $users,
        private RoleRepositoryInterface $roles,
    ) {}

    /**
     * @throws AdminRequiredException
     */
    public function assertMayAssign(UserId $actorId, Role $role): void
    {
        if ($role->isAdmin() && !$this->isAdmin($actorId)) {
            throw AdminRequiredException::create();
        }
    }

    /**
     * @throws AdminRequiredException
     */
    public function assertMayManage(UserId $actorId, User $target): void
    {
        $role = $this->roles->findById($target->roleId);
        if ($role !== null && $role->isAdmin() && !$this->isAdmin($actorId)) {
            throw AdminRequiredException::create();
        }
    }

    private function isAdmin(UserId $actorId): bool
    {
        $actor = $this->users->findById($actorId);
        $role = $actor === null ? null : $this->roles->findById($actor->roleId);

        return $role !== null && $role->isAdmin();
    }
}
