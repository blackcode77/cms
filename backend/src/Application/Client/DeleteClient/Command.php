<?php

declare(strict_types=1);

namespace App\Application\Client\DeleteClient;

use App\Domain\Client\Value\ClientId;

final readonly class Command
{
    public function __construct(
        public ClientId $id,
    ) {}
}
