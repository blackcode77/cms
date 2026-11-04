<?php

declare(strict_types=1);

namespace App\Application\Order\CreateOrder;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientPhone;
use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;

final readonly class Command
{
    public function __construct(
        public ClientPhone $phone,
        public ClientDetails $client,
        public OrderStatus $status,
        public ?UserId $managerId,
        public CargoDetails $cargo,
        public string $message,
        public string $comment,
    ) {}
}
