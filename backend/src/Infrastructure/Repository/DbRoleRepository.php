<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Role\Entity\Role;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\Table;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbRoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private EntityHydratorInterface $entityHydrator,
        private ConnectionInterface $db,
    ) {}

    public function findByName(RoleName $name): ?Role
    {
        /** @var array{id: string, name: non-empty-string}|null $row */
        $row = $this->db->createQuery()
            ->from(Table::ROLES)
            ->select(['id', 'name'])
            ->where(['name' => $name->toString()])
            ->one();
        if ($row === null) {
            return null;
        }

        /** @var list<string> $permissions */
        $permissions = $this->db->createQuery()
            ->from(Table::ROLE_PERMISSIONS)
            ->select('permission')
            ->where(['role_id' => $row['id']])
            ->column();

        return $this->entityHydrator->create(
            Role::class,
            [
                'id' => RoleId::fromString($row['id']),
                'name' => new RoleName($row['name']),
                'permissions' => array_map(Permission::from(...), $permissions),
            ],
        );
    }

    public function add(Role $role): void
    {
        $this->db->createCommand()
            ->insert(Table::ROLES, [
                'id' => $role->id->toString(),
                'name' => $role->name->toString(),
            ])
            ->execute();

        foreach ($role->permissions as $permission) {
            $this->db->createCommand()
                ->insert(Table::ROLE_PERMISSIONS, [
                    'role_id' => $role->id->toString(),
                    'permission' => $permission->value,
                ])
                ->execute();
        }
    }
}
