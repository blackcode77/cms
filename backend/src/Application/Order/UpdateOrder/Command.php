<?php

declare(strict_types=1);

namespace App\Application\Order\UpdateOrder;

use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;

final readonly class Command
{
    public function __construct(
        public OrderId $id,
        public OrderStatus $status,
        public ?UserId $managerId,
        public CargoDetails $cargo,
        public string $message,
        public string $comment,
    ) {}
}
