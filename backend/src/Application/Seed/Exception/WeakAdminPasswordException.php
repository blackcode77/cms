<?php

declare(strict_types=1);

namespace App\Application\Seed\Exception;

use RuntimeException;

final class WeakAdminPasswordException extends RuntimeException
{
    public static function create(): self
    {
        return new self(
            'ADMIN_PASSWORD is empty, shorter than 8 characters or a placeholder. Set a real password in .env.',
        );
    }
}
