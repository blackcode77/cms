<?php

declare(strict_types=1);

namespace App\Application\User\UpdateUser;

use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;

final readonly class Command
{
    public function __construct(
        public UserId $id,
        public UserEmail $email,
        public string $name,
        public RoleId $roleId,
        public UserId $actorId,
    ) {}
}
