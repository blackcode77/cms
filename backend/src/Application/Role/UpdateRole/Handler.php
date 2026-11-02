<?php

declare(strict_types=1);

namespace App\Application\Role\UpdateRole;

use App\Application\Role\Exception\RoleAlreadyExistsException;
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
     * @throws RoleAlreadyExistsException
     */
    public function handle(Command $command): void
    {
        $role = $this->roleRepository->findById($command->id) ?? throw RoleNotFoundException::create();

        if ($role->isAdmin()) {
            throw RoleProtectedException::adminIsReadOnly();
        }
        $renamed = $role->name->toString() !== $command->name->toString();
        if ($renamed && $role->isVisiter()) {
            throw RoleProtectedException::visiterCannotBeRenamed();
        }
        if ($renamed && $this->roleRepository->findByName($command->name) !== null) {
            throw RoleAlreadyExistsException::fromName($command->name);
        }

        $role->update($command->name, $command->permissions);
        $this->roleRepository->save($role);
    }
}
