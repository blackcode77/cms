<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbOrderRepository implements OrderRepositoryInterface
{
    private const string FORMAT = 'Y-m-d H:i:s.uP';

    public function __construct(
        private EntityHydratorInterface $entityHydrator,
        private ConnectionInterface $db,
    ) {}

    public function nextNumber(): int
    {
        return (int) $this->db->createCommand("SELECT nextval('order_number_seq')")->queryScalar();
    }

    public function findById(OrderId $id): ?Order
    {
        /**
         * @var array{
         *     id: string, number: int|string, client_id: string, status: string, manager_id: string|null,
         *     cargo_from: string, cargo_to: string, cargo_type: string, cargo_weight: string,
         *     message: string, comment: string, created_at: string, updated_at: string,
         * }|null $row
         */
        $row = $this->db->createQuery()
            ->from(Table::ORDERS)
            ->where(['id' => $id->toString()])
            ->one();
        if ($row === null) {
            return null;
        }

        return $this->entityHydrator->create(
            Order::class,
            [
                'id' => OrderId::fromString($row['id']),
                'number' => (int) $row['number'],
                'clientId' => ClientId::fromString($row['client_id']),
                'status' => OrderStatus::from($row['status']),
                'managerId' => UserId::tryFromString($row['manager_id']),
                'cargo' => new CargoDetails($row['cargo_from'], $row['cargo_to'], $row['cargo_type'], $row['cargo_weight']),
                'message' => $row['message'],
                'comment' => $row['comment'],
                'createdAt' => new DateTimeImmutable($row['created_at']),
                'updatedAt' => new DateTimeImmutable($row['updated_at']),
            ],
        );
    }

    public function add(Order $order): void
    {
        $this->db->createCommand()
            ->insert(Table::ORDERS, [
                'id' => $order->id->toString(),
                'number' => $order->number,
                'client_id' => $order->clientId->toString(),
                ...self::columns($order),
                'created_at' => $order->createdAt->format(self::FORMAT),
            ])
            ->execute();
    }

    public function update(Order $order): void
    {
        $this->db->createCommand()
            ->update(Table::ORDERS, self::columns($order), ['id' => $order->id->toString()])
            ->execute();
    }

    public function remove(OrderId $id): void
    {
        $this->db->createCommand()
            ->delete(Table::ORDERS, ['id' => $id->toString()])
            ->execute();
    }

    /**
     * @return array<string, string|null>
     */
    private static function columns(Order $order): array
    {
        return [
            'status' => $order->status->value,
            'manager_id' => $order->managerId?->toString(),
            'cargo_from' => $order->cargo->from,
            'cargo_to' => $order->cargo->to,
            'cargo_type' => $order->cargo->cargoType,
            'cargo_weight' => $order->cargo->weight,
            'message' => $order->message,
            'comment' => $order->comment,
            'updated_at' => $order->updatedAt->format(self::FORMAT),
        ];
    }
}
