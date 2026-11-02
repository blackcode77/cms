<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class CannotDeleteYourselfException extends Exception
{
    public static function create(): self
    {
        return new self('You cannot delete your own account.');
    }
}
