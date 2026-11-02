<?php

declare(strict_types=1);

namespace App\Application\Auth\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

/**
 * Deliberately identical for an unknown email and a wrong password.
 */
#[UserException]
final class InvalidCredentialsException extends Exception
{
    public static function create(): self
    {
        return new self('Invalid email or password.');
    }
}
