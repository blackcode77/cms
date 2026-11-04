<?php

declare(strict_types=1);

namespace App\Application\Order\CreateOrder;

use App\Application\Client\ClientResolver;
use App\Application\Order\Exception\ManagerNotAssignableException;
use App\Application\Order\ManagerPolicy;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Order\Value\OrderId;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

/**
 * An Order entered in the admin area; not subject to the Order request quota.
 */
final readonly class Handler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private ClientResolver $clientResolver,
        private ManagerPolicy $managerPolicy,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws ManagerNotAssignableException
     */
    public function handle(Command $command): Result
    {
        $this->managerPolicy->assertAssignable($command->managerId);
        $now = DateTimeImmutable::createFromInterface($this->clock->now());

        $clientId = $this->clientResolver->resolve($command->phone, $command->client, $now);
        $order = new Order(
            new OrderId(Uuid::uuid7()),
            $this->orders->nextNumber(),
            $clientId,
            $command->status,
            $command->managerId,
            $command->cargo,
            $command->message,
            $command->comment,
            $now,
        );
        $this->orders->add($order);

        return new Result($order->id, $order->number, $clientId);
    }
}
