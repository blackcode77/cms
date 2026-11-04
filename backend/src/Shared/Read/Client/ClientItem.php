<?php

declare(strict_types=1);

namespace App\Shared\Read\Client;

use App\Domain\Client\Value\ClientId;
use DateTimeImmutable;

final readonly class ClientItem
{
    public function __construct(
        public ClientId $id,
        public string $phone,
        public string $firstName,
        public string $lastName,
        public string $middleName,
        public ?string $email,
        public string $comment,
        public int $ordersCount,
        public DateTimeImmutable $createdAt,
    ) {}
}
