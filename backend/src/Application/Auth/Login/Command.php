<?php

declare(strict_types=1);

namespace App\Application\Auth\Login;

use App\Domain\User\Value\UserEmail;

final readonly class Command
{
    public function __construct(
        public UserEmail $email,
        public string $password,
        public string $ip,
    ) {}
}
