<?php

declare(strict_types=1);

namespace App\Shared\Read\Client;

use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\QueryInterface;

final readonly class ClientReader
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    /**
     * Newest first. The search matches the digits of the phone or any part of the name or email.
     *
     * @return list<ClientItem>
     */
    public function find(string $search = ''): array
    {
        $query = $this->query()->orderBy(['c.created_at' => SORT_DESC, 'c.id' => SORT_DESC]);
        $search = trim($search);
        if ($search !== '') {
            $condition = [
                'or',
                ['like', new Expression("concat_ws(' ', c.last_name, c.first_name, c.middle_name)"), $search, 'caseSensitive' => false],
                ['like', new Expression("concat_ws(' ', c.first_name, c.last_name)"), $search, 'caseSensitive' => false],
                ['like', 'c.email', $search, 'caseSensitive' => false],
            ];
            $digits = (string) preg_replace('/\D/', '', $search);
            if ($digits !== '') {
                $condition[] = ['like', 'c.phone', $digits];
            }
            $query->andWhere($condition);
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = $query->all();

        return array_map(self::item(...), $rows);
    }

    public function get(ClientId $id): ?ClientItem
    {
        /** @var array<string, mixed>|null $row */
        $row = $this->query()->andWhere(['c.id' => $id->toString()])->one();

        return $row === null ? null : self::item($row);
    }

    public function getByPhone(ClientPhone $phone): ?ClientItem
    {
        /** @var array<string, mixed>|null $row */
        $row = $this->query()->andWhere(['c.phone' => $phone->toString()])->one();

        return $row === null ? null : self::item($row);
    }

    private function query(): QueryInterface
    {
        $ordersCount = $this->db->createQuery()
            ->select('COUNT(*)')
            ->from(['o' => Table::ORDERS])
            ->where('o.client_id = c.id');

        return $this->db->createQuery()
            ->select([
                'id' => 'c.id',
                'phone' => 'c.phone',
                'first_name' => 'c.first_name',
                'last_name' => 'c.last_name',
                'middle_name' => 'c.middle_name',
                'email' => 'c.email',
                'comment' => 'c.comment',
                'orders_count' => $ordersCount,
                'created_at' => 'c.created_at',
            ])
            ->from(['c' => Table::CLIENTS])
            ->where('1 = 1');
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function item(array $row): ClientItem
    {
        /**
         * @var array{
         *     id: string, phone: string, first_name: string, last_name: string, middle_name: string,
         *     email: string|null, comment: string, orders_count: int|string, created_at: string,
         * } $row
         */
        return new ClientItem(
            ClientId::fromString($row['id']),
            $row['phone'],
            $row['first_name'],
            $row['last_name'],
            $row['middle_name'],
            $row['email'],
            $row['comment'],
            (int) $row['orders_count'],
            new DateTimeImmutable($row['created_at']),
        );
    }
}
