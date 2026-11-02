<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class RoleNotAssignableException extends Exception
{
    public static function create(): self
    {
        return new self('This Role does not exist or cannot be assigned to a User.');
    }
}
