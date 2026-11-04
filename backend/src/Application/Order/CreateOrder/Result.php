<?php

declare(strict_types=1);

namespace App\Application\Order\CreateOrder;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\OrderId;

final readonly class Result
{
    public function __construct(
        public OrderId $id,
        public int $number,
        public ClientId $clientId,
    ) {}
}
