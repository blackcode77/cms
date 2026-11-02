<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class AdminRequiredException extends Exception
{
    public static function create(): self
    {
        return new self('Only an Admin can assign the Admin Role or manage a User who holds it.');
    }
}
