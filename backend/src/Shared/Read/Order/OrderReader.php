<?php

declare(strict_types=1);

namespace App\Shared\Read\Order;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\Role\Value\Permission;
use App\Domain\User\Value\UserId;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\QueryInterface;

final readonly class OrderReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * Newest first.
     *
     * @return list<OrderItem>
     */
    public function find(OrderFilter $filter = new OrderFilter()): array
    {
        $query = $this->query()->orderBy(['o.created_at' => SORT_DESC, 'o.number' => SORT_DESC]);
        if ($filter->status !== null) {
            $query->andWhere(['o.status' => $filter->status->value]);
        }
        if ($filter->managerId !== null) {
            $query->andWhere(['o.manager_id' => $filter->managerId->toString()]);
        }
        if ($filter->clientId !== null) {
            $query->andWhere(['o.client_id' => $filter->clientId->toString()]);
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = $query->all();

        return array_map(self::item(...), $rows);
    }

    public function get(OrderId $id): ?OrderItem
    {
        /** @var array<string, mixed>|null $row */
        $row = $this->query()->andWhere(['o.id' => $id->toString()])->one();

        return $row === null ? null : self::item($row);
    }

    /**
     * Users who can be assigned as the Manager of an Order.
     *
     * @return list<ManagerOption>
     */
    public function managers(): array
    {
        /** @var list<array{id: string, name: string, email: string}> $rows */
        $rows = $this->db->createQuery()
            ->select(['id' => 'u.id', 'name' => 'u.name', 'email' => 'u.email'])
            ->from(['u' => Table::USERS])
            ->innerJoin(['p' => Table::ROLE_PERMISSIONS], 'p.role_id = u.role_id')
            ->where(['p.permission' => Permission::HandleOrders->value])
            ->orderBy(['u.name' => SORT_ASC, 'u.email' => SORT_ASC])
            ->all();

        return array_map(
            static fn(array $row) => new ManagerOption(UserId::fromString($row['id']), $row['name'], $row['email']),
            $rows,
        );
    }

    private function query(): QueryInterface
    {
        return $this->db->createQuery()
            ->select([
                'id' => 'o.id',
                'number' => 'o.number',
                'status' => 'o.status',
                'client_id' => 'o.client_id',
                'client_phone' => 'c.phone',
                'client_first_name' => 'c.first_name',
                'client_last_name' => 'c.last_name',
                'client_middle_name' => 'c.middle_name',
                'manager_id' => 'o.manager_id',
                'manager_name' => 'u.name',
                'cargo_from' => 'o.cargo_from',
                'cargo_to' => 'o.cargo_to',
                'cargo_type' => 'o.cargo_type',
                'cargo_weight' => 'o.cargo_weight',
                'message' => 'o.message',
                'comment' => 'o.comment',
                'created_at' => 'o.created_at',
                'updated_at' => 'o.updated_at',
            ])
            ->from(['o' => Table::ORDERS])
            ->innerJoin(['c' => Table::CLIENTS], 'c.id = o.client_id')
            ->leftJoin(['u' => Table::USERS], 'u.id = o.manager_id')
            ->where('1 = 1');
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function item(array $row): OrderItem
    {
        /**
         * @var array{
         *     id: string, number: int|string, status: string, client_id: string, client_phone: string,
         *     client_first_name: string, client_last_name: string, client_middle_name: string,
         *     manager_id: string|null, manager_name: string|null, cargo_from: string, cargo_to: string,
         *     cargo_type: string, cargo_weight: string, message: string, comment: string,
         *     created_at: string, updated_at: string,
         * } $row
         */
        return new OrderItem(
            OrderId::fromString($row['id']),
            (int) $row['number'],
            OrderStatus::from($row['status']),
            ClientId::fromString($row['client_id']),
            $row['client_phone'],
            $row['client_first_name'],
            $row['client_last_name'],
            $row['client_middle_name'],
            UserId::tryFromString($row['manager_id']),
            $row['manager_name'],
            new CargoDetails($row['cargo_from'], $row['cargo_to'], $row['cargo_type'], $row['cargo_weight']),
            $row['message'],
            $row['comment'],
            new DateTimeImmutable($row['created_at']),
            new DateTimeImmutable($row['updated_at']),
        );
    }
}
