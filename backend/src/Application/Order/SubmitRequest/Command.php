<?php

declare(strict_types=1);

namespace App\Application\Order\SubmitRequest;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientPhone;
use App\Domain\Order\Value\CargoDetails;

final readonly class Command
{
    public function __construct(
        public ClientPhone $phone,
        public ClientDetails $client,
        public CargoDetails $cargo,
        public string $message,
        public string $ip,
    ) {}
}
