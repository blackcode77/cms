<?php

declare(strict_types=1);

namespace App\Application\Shared;

/**
 * Generates the random passwords that are emailed to Users (16 characters, cryptographically secure).
 */
final readonly class PasswordGenerator
{
    public const int LENGTH = 16;

    // No look-alike characters (0/O, 1/l/I) since the password is typed from an email.
    private const string ALPHABET = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function generate(): string
    {
        $password = '';
        $max = strlen(self::ALPHABET) - 1;
        for ($i = 0; $i < self::LENGTH; $i++) {
            $password .= self::ALPHABET[random_int(0, $max)];
        }

        return $password;
    }
}
