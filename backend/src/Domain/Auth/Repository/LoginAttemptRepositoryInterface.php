<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repository;

use App\Domain\Auth\Entity\LoginAttempt;
use DateTimeImmutable;

interface LoginAttemptRepositoryInterface
{
    /**
     * Atomically counts one attempt of the key + IP pair and returns the state including it. Rows whose window
     * has ended are purged (so the table stays bounded) and a pair with an ended window starts afresh.
     */
    public function register(string $key, string $ip, DateTimeImmutable $now): LoginAttempt;

    public function clear(string $key, string $ip): void;
}
