<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidImageUrlException extends Exception
{
    public static function create(): self
    {
        return new self('Image must be an uploaded file or a bundled image (a path under /uploads/ or /img/).');
    }
}
