<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Role\Entity\Role;
use App\Domain\User\Entity\User;

final readonly class CurrentUser
{
    public function __construct(
        public User $user,
        public Role $role,
    ) {}
}
