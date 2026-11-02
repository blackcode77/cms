<?php

declare(strict_types=1);

namespace App\Application\Role\CreateRole;

use App\Application\Role\Exception\RoleAlreadyExistsException;
use App\Domain\Role\Entity\Role;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\Role\Value\RoleId;
use Ramsey\Uuid\Uuid;

final readonly class Handler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
    ) {}

    /**
     * @throws RoleAlreadyExistsException
     */
    public function handle(Command $command): Result
    {
        if ($this->roleRepository->findByName($command->name) !== null) {
            throw RoleAlreadyExistsException::fromName($command->name);
        }

        $id = new RoleId(Uuid::uuid7());
        $this->roleRepository->add(new Role($id, $command->name, $command->permissions));

        return new Result($id);
    }
}
