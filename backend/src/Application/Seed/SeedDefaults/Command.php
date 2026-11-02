<?php

declare(strict_types=1);

namespace App\Application\Seed\SeedDefaults;

use App\Domain\User\Value\UserEmail;

final readonly class Command
{
    public function __construct(
        public UserEmail $adminEmail,
        public string $adminPassword,
    ) {}
}
