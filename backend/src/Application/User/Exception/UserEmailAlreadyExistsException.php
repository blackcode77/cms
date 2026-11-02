<?php

declare(strict_types=1);

namespace App\Application\User\Exception;

use App\Domain\User\Value\UserEmail;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class UserEmailAlreadyExistsException extends Exception
{
    public static function fromEmail(UserEmail $email): self
    {
        return new self(sprintf('User with email "%s" already exists.', $email));
    }
}
