<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidColorException extends Exception
{
    public static function create(): self
    {
        return new self('Colour must be "#rrggbb", e.g. "#1f6fb2".');
    }
}
