<?php

declare(strict_types=1);

namespace App\Shared\Read\User;

use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Domain\User\Value\UserId;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class UserReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * @return list<UserItem>
     */
    public function find(): array
    {
        /** @var list<array{
         *     id: string, email: string, name: string, role_id: string, role_name: string,
         *     must_change_password: bool, created_at: string,
         * }> $rows
         */
        $rows = $this->db->createQuery()
            ->select([
                'id' => 'u.id',
                'email' => 'u.email',
                'name' => 'u.name',
                'role_id' => 'u.role_id',
                'role_name' => 'r.name',
                'must_change_password' => 'u.must_change_password',
                'created_at' => 'u.created_at',
            ])
            ->from(['u' => Table::USERS])
            ->innerJoin(['r' => Table::ROLES], 'r.id = u.role_id')
            ->orderBy(['u.created_at' => SORT_DESC, 'u.id' => SORT_DESC])
            ->all();

        return array_map(
            static fn(array $row) => new UserItem(
                UserId::fromString($row['id']),
                $row['email'],
                $row['name'],
                RoleId::fromString($row['role_id']),
                $row['role_name'],
                $row['must_change_password'],
                new DateTimeImmutable($row['created_at']),
            ),
            $rows,
        );
    }

    /**
     * @return list<RoleOption>
     */
    public function assignableRoles(bool $includeAdmin): array
    {
        /** @var list<array{id: string, name: string}> $rows */
        $query = $this->db->createQuery()
            ->select(['id', 'name'])
            ->from(Table::ROLES)
            ->where(['<>', 'name', RoleName::VISITER])
            ->orderBy(['name' => SORT_ASC]);
        if (!$includeAdmin) {
            $query->andWhere(['<>', 'name', RoleName::ADMIN]);
        }
        $rows = $query->all();

        return array_map(
            static fn(array $row) => new RoleOption(RoleId::fromString($row['id']), $row['name']),
            $rows,
        );
    }
}
