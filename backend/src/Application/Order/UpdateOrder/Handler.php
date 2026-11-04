<?php

declare(strict_types=1);

namespace App\Application\Order\UpdateOrder;

use App\Application\Order\Exception\ManagerNotAssignableException;
use App\Application\Order\Exception\OrderNotFoundException;
use App\Application\Order\ManagerPolicy;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ManagerPolicy $managerPolicy,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws OrderNotFoundException
     * @throws ManagerNotAssignableException
     */
    public function handle(Command $command): void
    {
        $order = $this->orders->findById($command->id) ?? throw OrderNotFoundException::create();

        // keeping a Manager who has since lost the Permission is allowed, assigning a new one is checked
        if ($command->managerId !== null && ($order->managerId === null || !$order->managerId->isEqualTo($command->managerId))) {
            $this->managerPolicy->assertAssignable($command->managerId);
        }

        $order->edit(
            $command->status,
            $command->managerId,
            $command->cargo,
            $command->message,
            $command->comment,
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );
        $this->orders->update($order);
    }
}
