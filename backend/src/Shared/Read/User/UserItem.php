<?php

declare(strict_types=1);

namespace App\Shared\Read\User;

use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;

final readonly class UserItem
{
    public function __construct(
        public UserId $id,
        public string $email,
        public string $name,
        public RoleId $roleId,
        public string $roleName,
        public bool $mustChangePassword,
        public DateTimeImmutable $createdAt,
    ) {}
}
