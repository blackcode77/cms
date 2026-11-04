<?php

declare(strict_types=1);

namespace App\Application\Shared;

use DateTimeImmutable;

/**
 * Counts hits per key in fixed windows.
 */
interface RateLimiterInterface
{
    /**
     * Atomically counts one hit of the key and tells whether it is still within the limit.
     *
     * @param positive-int $limit hits allowed per window
     * @param positive-int $windowSeconds
     */
    public function hit(string $key, int $limit, int $windowSeconds, DateTimeImmutable $now): bool;
}
