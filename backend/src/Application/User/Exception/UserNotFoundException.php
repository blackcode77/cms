<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class UserNotFoundException extends Exception
{
    public static function create(): self
    {
        return new self('User not found.');
    }
}
