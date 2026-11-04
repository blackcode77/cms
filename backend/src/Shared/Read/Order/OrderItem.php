<?php

declare(strict_types=1);

namespace App\Shared\Read\Order;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;

final readonly class OrderItem
{
    public function __construct(
        public OrderId $id,
        public int $number,
        public OrderStatus $status,
        public ClientId $clientId,
        public string $clientPhone,
        public string $clientFirstName,
        public string $clientLastName,
        public string $clientMiddleName,
        public ?UserId $managerId,
        public ?string $managerName,
        public CargoDetails $cargo,
        public string $message,
        public string $comment,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}
}
