<?php

declare(strict_types=1);

namespace App\Application\Client\UpdateClient;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;

final readonly class Command
{
    public function __construct(
        public ClientId $id,
        public ClientPhone $phone,
        public ClientDetails $details,
        public string $comment,
    ) {}
}
