<?php

declare(strict_types=1);

namespace App\Application\Client\CreateClient;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientPhone;

final readonly class Command
{
    public function __construct(
        public ClientPhone $phone,
        public ClientDetails $details,
        public string $comment,
    ) {}
}
