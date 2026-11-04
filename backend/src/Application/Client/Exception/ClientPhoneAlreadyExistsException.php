<?php

declare(strict_types=1);

namespace App\Application\Client\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class ClientPhoneAlreadyExistsException extends Exception
{
    public static function create(): self
    {
        return new self('Клиент с таким телефоном уже существует.');
    }
}
