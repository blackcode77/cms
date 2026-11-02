<?php

declare(strict_types=1);

namespace App\Application\Role\DeleteRole;

use App\Application\Role\Exception\RoleInUseException;
use App\Application\Role\Exception\RoleNotFoundException;
use App\Application\Role\Exception\RoleProtectedException;
use App\Domain\Role\Repository\RoleRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
    ) {}

    /**
     * @throws RoleNotFoundException
     * @throws RoleProtectedException
     * @throws RoleInUseException
     */
    public function handle(Command $command): void
    {
        $role = $this->roleRepository->findById($command->id) ?? throw RoleNotFoundException::create();

        if ($role->isAdmin()) {
            throw RoleProtectedException::adminIsReadOnly();
        }
        if ($role->isVisiter()) {
            throw RoleProtectedException::visiterCannotBeDeleted();
        }
        $usersCount = $this->roleRepository->countUsers($role->id);
        if ($usersCount > 0) {
            throw RoleInUseException::fromCount($role->name, $usersCount);
        }

        $this->roleRepository->delete($role->id);
    }
}
