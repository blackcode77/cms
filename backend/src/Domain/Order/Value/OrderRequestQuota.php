<?php

declare(strict_types=1);

namespace App\Domain\Order\Value;

use DateInterval;
use DateTimeImmutable;

/**
 * Anti-spam rule for Order requests: at most {@see self::MAX_REQUESTS} from one address and at most as many
 * for one phone number within the last {@see self::WINDOW} (rolling). Orders created in the admin area do not count.
 */
final readonly class OrderRequestQuota
{
    public const int MAX_REQUESTS = 3;
    public const string WINDOW = 'P1D';

    public static function windowStart(DateTimeImmutable $now): DateTimeImmutable
    {
        return $now->sub(new DateInterval(self::WINDOW));
    }

    public static function isExhausted(int $requestsInWindow): bool
    {
        return $requestsInWindow >= self::MAX_REQUESTS;
    }
}
