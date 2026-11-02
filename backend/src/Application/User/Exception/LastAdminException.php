<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class LastAdminException extends Exception
{
    public static function create(): self
    {
        return new self('The last Admin cannot be deleted or demoted.');
    }
}
