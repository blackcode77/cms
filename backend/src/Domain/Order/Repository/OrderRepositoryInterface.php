<?php

declare(strict_types=1);

namespace App\Domain\Order\Repository;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Value\OrderId;

interface OrderRepositoryInterface
{
    /**
     * Reserves the next Order number (numbers are never reused, gaps are possible).
     */
    public function nextNumber(): int;

    public function findById(OrderId $id): ?Order;

    public function add(Order $order): void;

    public function update(Order $order): void;

    public function remove(OrderId $id): void;
}
