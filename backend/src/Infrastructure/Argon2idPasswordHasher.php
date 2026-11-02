<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\PasswordHasherInterface;

final readonly class Argon2idPasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_ARGON2ID);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
