<?php

declare(strict_types=1);

namespace App\Application\Client\Exception;

use Exception;

/**
 * Not a business error: the API answers it with 404.
 */
final class ClientNotFoundException extends Exception
{
    public static function create(): self
    {
        return new self('Клиент не найден.');
    }
}
