<?php

declare(strict_types=1);

namespace App\Shared\Read\Order;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;

final readonly class OrderFilter
{
    public function __construct(
        public ?OrderStatus $status = null,
        public ?UserId $managerId = null,
        public ?ClientId $clientId = null,
    ) {}
}
