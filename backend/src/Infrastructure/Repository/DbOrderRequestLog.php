<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Client\Value\ClientPhone;
use App\Domain\Order\Repository\OrderRequestLogInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbOrderRequestLog implements OrderRequestLogInterface
{
    private const string FORMAT = 'Y-m-d H:i:s.uP';

    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function countFromIpSince(string $ip, DateTimeImmutable $since): int
    {
        return $this->countSince(['ip' => $ip], $since);
    }

    public function countForPhoneSince(ClientPhone $phone, DateTimeImmutable $since): int
    {
        return $this->countSince(['phone' => $phone->toString()], $since);
    }

    public function record(string $ip, ClientPhone $phone, DateTimeImmutable $now, DateTimeImmutable $forgetBefore): void
    {
        $this->db->createCommand()
            ->delete(Table::ORDER_REQUEST_LOG, ['<', 'created_at', $forgetBefore->format(self::FORMAT)])
            ->execute();
        $this->db->createCommand()
            ->insert(Table::ORDER_REQUEST_LOG, [
                'ip' => $ip,
                'phone' => $phone->toString(),
                'created_at' => $now->format(self::FORMAT),
            ])
            ->execute();
    }

    /**
     * @param array<string, string> $condition
     */
    private function countSince(array $condition, DateTimeImmutable $since): int
    {
        return (int) $this->db->createQuery()
            ->from(Table::ORDER_REQUEST_LOG)
            ->where($condition)
            ->andWhere(['>', 'created_at', $since->format(self::FORMAT)])
            ->count();
    }
}
