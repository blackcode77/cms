<?php

declare(strict_types=1);

namespace App\Application\Auth\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class LoginLockedException extends Exception
{
    public static function create(): self
    {
        return new self('Too many failed sign-in attempts. Try again in 15 minutes.');
    }

    public static function forPasswordChange(): self
    {
        return new self('Too many failed password attempts. Try again in 15 minutes.');
    }
}
