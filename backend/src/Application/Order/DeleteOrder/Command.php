<?php

declare(strict_types=1);

namespace App\Application\Order\DeleteOrder;

use App\Domain\Order\Value\OrderId;

final readonly class Command
{
    public function __construct(
        public OrderId $id,
    ) {}
}
