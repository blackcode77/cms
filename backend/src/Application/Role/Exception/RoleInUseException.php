<?php

declare(strict_types=1);

namespace App\Application\Role\Exception;

use App\Domain\Role\Value\RoleName;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class RoleInUseException extends Exception
{
    public static function fromCount(RoleName $name, int $usersCount): self
    {
        return new self(sprintf(
            'Role "%s" cannot be deleted: it is held by %d %s.',
            $name,
            $usersCount,
            $usersCount === 1 ? 'user' : 'users',
        ));
    }
}
