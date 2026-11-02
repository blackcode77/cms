<?php

declare(strict_types=1);

namespace App\Shared\Read\Role;

use App\Domain\Role\Value\Permission;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Infrastructure\Table;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class RoleReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * @return list<RoleItem>
     */
    public function find(): array
    {
        /** @var list<array{id: string, name: non-empty-string}> $roles */
        $roles = $this->db->createQuery()
            ->select(['id', 'name'])
            ->from(Table::ROLES)
            ->orderBy(['name' => SORT_ASC])
            ->all();

        /** @var list<array{role_id: string, permission: string}> $permissionRows */
        $permissionRows = $this->db->createQuery()
            ->select(['role_id', 'permission'])
            ->from(Table::ROLE_PERMISSIONS)
            ->all();
        $held = [];
        foreach ($permissionRows as $row) {
            $held[$row['role_id']][$row['permission']] = true;
        }

        /** @var list<array{role_id: string, count: int|string}> $countRows */
        $countRows = $this->db->createQuery()
            ->select(['role_id', 'count' => 'COUNT(*)'])
            ->from(Table::USERS)
            ->groupBy('role_id')
            ->all();
        $counts = array_column($countRows, 'count', 'role_id');

        return array_map(
            static fn(array $role): RoleItem => new RoleItem(
                RoleId::fromString($role['id']),
                new RoleName($role['name']),
                array_values(array_filter(
                    Permission::cases(),
                    static fn(Permission $p): bool => isset($held[$role['id']][$p->value]),
                )),
                (int) ($counts[$role['id']] ?? 0),
            ),
            $roles,
        );
    }
}
