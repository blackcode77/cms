<?php

declare(strict_types=1);

namespace App\Application\Order\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class ManagerNotAssignableException extends Exception
{
    public static function create(): self
    {
        return new self('Этого пользователя нельзя назначить менеджером заказа.');
    }
}
