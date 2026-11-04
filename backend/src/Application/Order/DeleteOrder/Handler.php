<?php

declare(strict_types=1);

namespace App\Application\Order\DeleteOrder;

use App\Application\Order\Exception\OrderNotFoundException;
use App\Domain\Order\Repository\OrderRepositoryInterface;

/**
 * The Client stays, even without Orders left.
 */
final readonly class Handler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
    ) {}

    /**
     * @throws OrderNotFoundException
     */
    public function handle(Command $command): void
    {
        if ($this->orders->findById($command->id) === null) {
            throw OrderNotFoundException::create();
        }
        $this->orders->remove($command->id);
    }
}
