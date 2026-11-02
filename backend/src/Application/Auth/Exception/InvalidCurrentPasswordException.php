<?php

declare(strict_types=1);

namespace App\Application\Auth\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidCurrentPasswordException extends Exception
{
    public static function create(): self
    {
        return new self('Current password is incorrect.');
    }
}
