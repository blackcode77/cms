<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application;

use App\Application\Seed\SeedDefaults\Command;
use App\Application\Seed\SeedDefaults\Handler;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Role\Entity\Role;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;
use App\Infrastructure\SystemClock;
use Codeception\Test\Unit;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class SeedDefaultsHandlerTest extends Unit
{
    public function testSeedsDefaultRolesAndInitialAdmin(): void
    {
        [$roles, $users, $handler] = $this->build();

        $handler->handle(new Command(new UserEmail('Admin@Example.com'), 'secret'));

        assertCount(3, $roles->roles);
        assertCount(6, $roles->roles['admin']->permissions);
        assertSame(
            [Permission::ViewDashboard, Permission::EditPage],
            $roles->roles['manager']->permissions,
        );
        assertSame([], $roles->roles['visiter']->permissions);
        assertCount(1, $users->users);
        $admin = $users->users[0];
        assertSame('admin@example.com', $admin->email->toString());
        assertTrue($admin->roleId->isEqualTo($roles->roles['admin']->id));
        assertSame('hashed:secret', $admin->passwordHash);
    }

    public function testIsIdempotentAndDoesNotOverwriteExistingAdmin(): void
    {
        [$roles, $users, $handler] = $this->build();
        $handler->handle(new Command(new UserEmail('admin@example.com'), 'first'));
        $handler->handle(new Command(new UserEmail('admin@example.com'), 'second'));

        assertCount(3, $roles->roles);
        assertCount(1, $users->users);
        assertSame('hashed:first', $users->users[0]->passwordHash);
    }

    /**
     * @return array{InMemoryRoleRepository, InMemoryUserRepository, Handler}
     */
    private function build(): array
    {
        $roles = new InMemoryRoleRepository();
        $users = new InMemoryUserRepository();
        $hasher = new class implements PasswordHasherInterface {
            public function hash(string $plain): string
            {
                return 'hashed:' . $plain;
            }

            public function verify(string $plain, string $hash): bool
            {
                return $hash === 'hashed:' . $plain;
            }
        };

        return [$roles, $users, new Handler($roles, $users, $hasher, new SystemClock())];
    }
}

final class InMemoryRoleRepository implements RoleRepositoryInterface
{
    /** @var array<string, Role> */
    public array $roles = [];

    public function findById(RoleId $id): ?Role
    {
        return array_find($this->roles, static fn(Role $r) => $r->id->toString() === $id->toString());
    }

    public function findByName(RoleName $name): ?Role
    {
        return $this->roles[$name->toString()] ?? null;
    }

    public function add(Role $role): void
    {
        $this->roles[$role->name->toString()] = $role;
    }

    public function save(Role $role): void {}

    public function delete(RoleId $id): void {}

    public function countUsers(RoleId $id): int
    {
        return 0;
    }
}

final class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var list<User> */
    public array $users = [];

    public function findByEmail(UserEmail $email): ?User
    {
        return array_find($this->users, static fn(User $u) => $u->email->toString() === $email->toString());
    }

    public function findById(UserId $id): ?User
    {
        return array_find($this->users, static fn(User $u) => $u->id->toString() === $id->toString());
    }

    public function hasByEmail(UserEmail $email): bool
    {
        return array_any($this->users, static fn(User $u) => $u->email->toString() === $email->toString());
    }

    public function add(User $user): void
    {
        $this->users[] = $user;
    }

    public function update(User $user): void {}

    public function remove(UserId $id): void {}

    public function countByRole(RoleId $roleId): int
    {
        return 0;
    }
    public function changePassword(UserId $id, string $passwordHash, bool $mustChangePassword): void {}
}
