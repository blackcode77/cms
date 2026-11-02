<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser;

use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;

final readonly class Command
{
    public function __construct(
        public UserEmail $email,
        public string $name,
        public RoleId $roleId,
    ) {}
}
