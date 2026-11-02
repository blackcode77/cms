<?php

declare(strict_types=1);

namespace App\Application\Role\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class RoleProtectedException extends Exception
{
    public static function adminIsReadOnly(): self
    {
        return new self('The Admin Role is read-only.');
    }

    public static function visiterCannotBeDeleted(): self
    {
        return new self('The Visiter Role cannot be deleted.');
    }

    public static function visiterCannotBeRenamed(): self
    {
        return new self('The Visiter Role cannot be renamed.');
    }
}
