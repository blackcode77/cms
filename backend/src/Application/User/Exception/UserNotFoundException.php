<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;

/**
 * Not a business error: the API answers it with 404.
 */
final class UserNotFoundException extends Exception
{
    public static function create(): self
    {
        return new self('User not found.');
    }
}
