<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\RateLimiterInterface;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

/**
 * Fixed windows aligned to multiples of the window length; one row per key, reset when a new window starts.
 */
final readonly class DbRateLimiter implements RateLimiterInterface
{
    private const string FORMAT = 'Y-m-d H:i:s.uP';
    /** Rows untouched for this long are purged (longer than any window in use). */
    private const int STALE_AFTER_SECONDS = 86400;

    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function hit(string $key, int $limit, int $windowSeconds, DateTimeImmutable $now): bool
    {
        $timestamp = $now->getTimestamp();
        $windowStart = $now->setTimestamp($timestamp - $timestamp % $windowSeconds);

        $this->db->createCommand()
            ->delete(Table::RATE_LIMIT_HITS, [
                '<', 'window_start', $now->setTimestamp($timestamp - self::STALE_AFTER_SECONDS)->format(self::FORMAT),
            ])
            ->execute();

        // single statement: concurrent hits cannot both read the same counter
        $hits = $this->db->createCommand(
            'INSERT INTO ' . Table::RATE_LIMIT_HITS . ' AS t (key, window_start, hits)
             VALUES (:key, :start, 1)
             ON CONFLICT (key) DO UPDATE SET
                 hits = CASE WHEN t.window_start = EXCLUDED.window_start THEN t.hits + 1 ELSE 1 END,
                 window_start = EXCLUDED.window_start
             RETURNING hits',
            ['key' => mb_substr($key, 0, 255), 'start' => $windowStart->format(self::FORMAT)],
        )->queryScalar();

        return (int) $hits <= $limit;
    }
}
