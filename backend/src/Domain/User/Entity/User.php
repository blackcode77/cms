<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;

final class User
{
    public readonly UserId $id;
    public private(set) UserEmail $email;
    public private(set) string $name;
    public private(set) string $passwordHash;
    public private(set) RoleId $roleId;
    public private(set) bool $mustChangePassword;
    public readonly DateTimeImmutable $createdAt;

    public function __construct(
        UserId $id,
        UserEmail $email,
        string $name,
        string $passwordHash,
        RoleId $roleId,
        bool $mustChangePassword,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
        $this->mustChangePassword = $mustChangePassword;
        $this->createdAt = $createdAt;
    }
}
