<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repository;

use App\Domain\Auth\Entity\LoginAttempt;

interface LoginAttemptRepositoryInterface
{
    public function find(string $email, string $ip): ?LoginAttempt;

    /**
     * Inserts or updates the attempt counter of the email + IP pair.
     */
    public function save(LoginAttempt $attempt): void;

    public function clear(string $email, string $ip): void;
}
