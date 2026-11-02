<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Value\UserEmail;

interface UserRepositoryInterface
{
    public function hasByEmail(UserEmail $email): bool;

    public function add(User $user): void;
}
