<?php

declare(strict_types=1);

namespace App\Domain\Auth\Entity;

use DateInterval;
use DateTimeImmutable;

/**
 * Consecutive failed sign-ins of one email + IP pair; too many lock the pair for a while.
 */
final class LoginAttempt
{
    public const int MAX_FAILURES = 5;
    public const string LOCK_DURATION = 'PT15M';

    public readonly string $email;
    public readonly string $ip;
    public private(set) int $failures;
    public private(set) ?DateTimeImmutable $lockedUntil;

    public function __construct(string $email, string $ip, int $failures = 0, ?DateTimeImmutable $lockedUntil = null)
    {
        $this->email = $email;
        $this->ip = $ip;
        $this->failures = $failures;
        $this->lockedUntil = $lockedUntil;
    }

    public function isLocked(DateTimeImmutable $now): bool
    {
        return $this->lockedUntil !== null && $this->lockedUntil > $now;
    }

    public function registerFailure(DateTimeImmutable $now): void
    {
        if ($this->lockedUntil !== null && $this->lockedUntil <= $now) {
            // the previous lock has run out: start counting afresh
            $this->failures = 0;
            $this->lockedUntil = null;
        }

        $this->failures++;
        if ($this->failures >= self::MAX_FAILURES) {
            $this->lockedUntil = $now->add(new DateInterval(self::LOCK_DURATION));
        }
    }
}
