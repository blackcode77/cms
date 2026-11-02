<?php

declare(strict_types=1);

namespace App\Infrastructure;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Clock used in the test environment: tests can freeze it at any moment (the state outlives the in-process requests).
 */
final class ShiftableClock implements ClockInterface
{
    private static ?DateTimeImmutable $fixed = null;

    public static function set(DateTimeImmutable $now): void
    {
        self::$fixed = $now;
    }

    public static function reset(): void
    {
        self::$fixed = null;
    }

    public function now(): DateTimeImmutable
    {
        return self::$fixed ?? new DateTimeImmutable();
    }
}
