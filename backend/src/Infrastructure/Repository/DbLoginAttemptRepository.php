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
    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function find(string $email, string $ip): ?LoginAttempt
    {
        /** @var array{failures: int|string, locked_until: string|null}|null $row */
        $row = $this->db->createQuery()
            ->from(Table::LOGIN_ATTEMPTS)
            ->where(['email' => $email, 'ip' => $ip])
            ->one();

        return $row === null
            ? null
            : new LoginAttempt(
                $email,
                $ip,
                (int) $row['failures'],
                $row['locked_until'] === null ? null : new DateTimeImmutable($row['locked_until']),
            );
    }

    public function save(LoginAttempt $attempt): void
    {
        $this->db->createCommand(
            'INSERT INTO ' . Table::LOGIN_ATTEMPTS . ' (email, ip, failures, locked_until)
             VALUES (:email, :ip, :failures, :locked)
             ON CONFLICT (email, ip) DO UPDATE SET failures = EXCLUDED.failures, locked_until = EXCLUDED.locked_until',
            [
                'email' => $attempt->email,
                'ip' => $attempt->ip,
                'failures' => $attempt->failures,
                'locked' => $attempt->lockedUntil?->format('Y-m-d H:i:s.uP'),
            ],
        )->execute();
    }

    public function clear(string $email, string $ip): void
    {
        $this->db->createCommand()
            ->delete(Table::LOGIN_ATTEMPTS, ['email' => $email, 'ip' => $ip])
            ->execute();
    }
}
