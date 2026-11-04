<?php

declare(strict_types=1);

namespace App\Shared\Read\Order;

use App\Domain\User\Value\UserId;

final readonly class ManagerOption
{
    public function __construct(
        public UserId $id,
        public string $name,
        public string $email,
    ) {}
}
