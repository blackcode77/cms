<?php

declare(strict_types=1);

namespace App\Application\Seed\SeedDefaults;

use App\Application\Seed\Exception\WeakAdminPasswordException;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Role\Entity\Role;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

/**
 * Idempotent: creates only what is missing, never overwrites existing Roles or Users.
 */
final readonly class Handler
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws WeakAdminPasswordException when the Initial admin would be created with a weak or placeholder password
     */
    public function handle(Command $command): void
    {
        $adminRole = null;
        foreach (self::defaultRoles() as $name => $permissions) {
            $roleName = new RoleName($name);
            $role = $this->roleRepository->findByName($roleName);
            if ($role === null) {
                $role = new Role(new RoleId(Uuid::uuid7()), $roleName, $permissions);
                $this->roleRepository->add($role);
            }
            if ($role->isAdmin()) {
                $adminRole = $role;
            }
        }

        if ($adminRole === null || $this->userRepository->hasByEmail($command->adminEmail)) {
            return;
        }

        if (strlen($command->adminPassword) < 8 || str_starts_with(strtolower($command->adminPassword), 'change-me')) {
            throw WeakAdminPasswordException::create();
        }

        $this->userRepository->add(
            new User(
                new UserId(Uuid::uuid7()),
                $command->adminEmail,
                'Admin',
                $this->passwordHasher->hash($command->adminPassword),
                $adminRole->id,
                false,
                DateTimeImmutable::createFromInterface($this->clock->now()),
            ),
        );
    }

    /**
     * @return array<non-empty-string, list<Permission>>
     */
    private static function defaultRoles(): array
    {
        return [
            RoleName::ADMIN => Permission::cases(),
            RoleName::MANAGER => [
                Permission::ViewDashboard,
                Permission::EditPage,
                Permission::ViewOrders,
                Permission::ManageOrders,
                Permission::HandleOrders,
                Permission::ViewClients,
                Permission::ManageClients,
            ],
            RoleName::VISITER => [],
        ];
    }
}
