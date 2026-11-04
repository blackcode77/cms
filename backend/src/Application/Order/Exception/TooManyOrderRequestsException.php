<?php

declare(strict_types=1);

namespace App\Application\Order\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class TooManyOrderRequestsException extends Exception
{
    public static function create(): self
    {
        return new self('Вы уже отправили несколько запросов, мы скоро с вами свяжемся.');
    }
}
