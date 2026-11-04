<?php

declare(strict_types=1);

namespace App\Domain\Order\Repository;

use App\Domain\Client\Value\ClientPhone;
use DateTimeImmutable;

/**
 * Accepted Order requests, kept only as long as {@see \App\Domain\Order\Value\OrderRequestQuota} needs them.
 */
interface OrderRequestLogInterface
{
    public function countFromIpSince(string $ip, DateTimeImmutable $since): int;

    public function countForPhoneSince(ClientPhone $phone, DateTimeImmutable $since): int;

    /**
     * Records an accepted request and forgets those older than $forgetBefore.
     */
    public function record(string $ip, ClientPhone $phone, DateTimeImmutable $now, DateTimeImmutable $forgetBefore): void;
}
