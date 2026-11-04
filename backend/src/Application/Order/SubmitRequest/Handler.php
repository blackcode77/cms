<?php

declare(strict_types=1);

namespace App\Application\Order\SubmitRequest;

use App\Application\Client\ClientResolver;
use App\Application\Order\Exception\TooManyOrderRequestsException;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepositoryInterface;
use App\Domain\Order\Repository\OrderRequestLogInterface;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderRequestQuota;
use App\Domain\Order\Value\OrderStatus;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

/**
 * An Order request from the public site: creates an Order with status «Заявка» for the Client with that phone.
 */
final readonly class Handler
{
    public function __construct(
        private OrderRepositoryInterface $orders,
        private OrderRequestLogInterface $log,
        private ClientResolver $clientResolver,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws TooManyOrderRequestsException
     */
    public function handle(Command $command): void
    {
        $now = DateTimeImmutable::createFromInterface($this->clock->now());
        $since = OrderRequestQuota::windowStart($now);
        if (
            OrderRequestQuota::isExhausted($this->log->countFromIpSince($command->ip, $since))
            || OrderRequestQuota::isExhausted($this->log->countForPhoneSince($command->phone, $since))
        ) {
            throw TooManyOrderRequestsException::create();
        }

        $clientId = $this->clientResolver->resolve($command->phone, $command->client, $now);
        $this->orders->add(
            new Order(
                new OrderId(Uuid::uuid7()),
                $this->orders->nextNumber(),
                $clientId,
                OrderStatus::Request,
                null,
                $command->cargo,
                $command->message,
                '',
                $now,
            ),
        );
        $this->log->record($command->ip, $command->phone, $now, $since);
    }
}
