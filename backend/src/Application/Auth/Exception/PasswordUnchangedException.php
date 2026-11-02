<?php

declare(strict_types=1);

namespace App\Application\Auth\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class PasswordUnchangedException extends Exception
{
    public static function create(): self
    {
        return new self('The new password must differ from the current one.');
    }
}
