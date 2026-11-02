<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbLoginAttemptRepository implements LoginAttemptRepositoryInterface
{
    private const string FORMAT = 'Y-m-d H:i:s.uP';

    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function register(string $key, string $ip, DateTimeImmutable $now): LoginAttempt
    {
        $this->db->createCommand()
            ->delete(Table::LOGIN_ATTEMPTS, ['<=', 'window_until', $now->format(self::FORMAT)])
            ->execute();

        // single statement: concurrent attempts cannot both read the same counter
        /** @var array{failures: int|string, window_until: string}|null $row */
        $row = $this->db->createCommand(
            'INSERT INTO ' . Table::LOGIN_ATTEMPTS . ' (email, ip, failures, window_until)
             VALUES (:key, :ip, 1, :until)
             ON CONFLICT (email, ip) DO UPDATE SET failures = ' . Table::LOGIN_ATTEMPTS . '.failures + 1
             RETURNING failures, window_until',
            [
                'key' => $key,
                'ip' => $ip,
                'until' => LoginAttempt::windowEnd($now)->format(self::FORMAT),
            ],
        )->queryOne();

        return new LoginAttempt($key, $ip, (int) ($row['failures'] ?? 1), new DateTimeImmutable($row['window_until'] ?? 'now'));
    }

    public function clear(string $key, string $ip): void
    {
        $this->db->createCommand()
            ->delete(Table::LOGIN_ATTEMPTS, ['email' => $key, 'ip' => $ip])
            ->execute();
    }
}
