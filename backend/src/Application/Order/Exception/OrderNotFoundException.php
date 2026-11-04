<?php

declare(strict_types=1);

namespace App\Application\Order\Exception;

use Exception;

/**
 * Not a business error: the API answers it with 404.
 */
final class OrderNotFoundException extends Exception
{
    public static function create(): self
    {
        return new self('Заказ не найден.');
    }
}
