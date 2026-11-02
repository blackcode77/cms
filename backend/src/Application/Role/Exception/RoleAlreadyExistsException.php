<?php

declare(strict_types=1);

namespace App\Application\Role\Exception;

use App\Domain\Role\Value\RoleName;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class RoleAlreadyExistsException extends Exception
{
    public static function fromName(RoleName $name): self
    {
        return new self(sprintf('Role "%s" already exists.', $name));
    }
}
