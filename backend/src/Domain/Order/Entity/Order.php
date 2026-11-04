<?php

declare(strict_types=1);

namespace App\Domain\Order\Entity;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\CargoDetails;
use App\Domain\Order\Value\OrderId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;

final class Order
{
    public readonly OrderId $id;
    /** Order number, at least four digits. */
    public readonly int $number;
    public readonly ClientId $clientId;
    public private(set) OrderStatus $status;
    public private(set) ?UserId $managerId;
    public private(set) CargoDetails $cargo;
    /** What the Client wrote. */
    public private(set) string $message;
    /** Internal note, never shown to the Client. */
    public private(set) string $comment;
    public readonly DateTimeImmutable $createdAt;
    public private(set) DateTimeImmutable $updatedAt;

    public function __construct(
        OrderId $id,
        int $number,
        ClientId $clientId,
        OrderStatus $status,
        ?UserId $managerId,
        CargoDetails $cargo,
        string $message,
        string $comment,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->clientId = $clientId;
        $this->status = $status;
        $this->managerId = $managerId;
        $this->cargo = $cargo;
        $this->message = $message;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public function edit(
        OrderStatus $status,
        ?UserId $managerId,
        CargoDetails $cargo,
        string $message,
        string $comment,
        DateTimeImmutable $now,
    ): void {
        $this->status = $status;
        $this->managerId = $managerId;
        $this->cargo = $cargo;
        $this->message = $message;
        $this->comment = $comment;
        $this->updatedAt = $now;
    }
}
