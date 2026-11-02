<?php

declare(strict_types=1);

namespace App\Domain\Auth\Entity;

use DateInterval;
use DateTimeImmutable;

/**
 * Sign-in attempts of one key + IP pair inside a fixed window. Attempts are counted before the password is
 * checked (and cleared on success), so more than {@see self::MAX_ATTEMPTS} in a window lock the pair until
 * the window ends.
 */
final readonly class LoginAttempt
{
    public const int MAX_ATTEMPTS = 5;
    public const string WINDOW = 'PT15M';

    public function __construct(
        public string $key,
        public string $ip,
        public int $attempts,
        public DateTimeImmutable $windowUntil,
    ) {}

    public static function windowEnd(DateTimeImmutable $now): DateTimeImmutable
    {
        return $now->add(new DateInterval(self::WINDOW));
    }

    public function isLocked(): bool
    {
        return $this->attempts > self::MAX_ATTEMPTS;
    }
}
