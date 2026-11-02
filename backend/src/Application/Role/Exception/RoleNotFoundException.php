<?php

declare(strict_types=1);

namespace App\Application\Role\Exception;

use Exception;

/**
 * Not a business error: the API answers it with 404.
 */
final class RoleNotFoundException extends Exception
{
    public static function create(): self
    {
        return new self('Role not found.');
    }
}
