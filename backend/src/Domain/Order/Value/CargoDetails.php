<?php

declare(strict_types=1);

namespace App\Domain\Order\Value;

/**
 * What the Client wants to ship, as typed by them; every part is optional (empty string = not given).
 */
final readonly class CargoDetails
{
    public const int LENGTH_LIMIT = 255;

    public function __construct(
        public string $from = '',
        public string $to = '',
        public string $cargoType = '',
        public string $weight = '',
    ) {}
}
