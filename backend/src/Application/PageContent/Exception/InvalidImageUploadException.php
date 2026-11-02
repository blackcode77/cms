<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidImageUploadException extends Exception
{
    public static function tooLarge(int $limitBytes): self
    {
        return new self(sprintf('Image must not exceed %d MB.', intdiv($limitBytes, 1024 * 1024)));
    }

    public static function unsupportedFormat(): self
    {
        return new self('Only JPEG, PNG and WebP images are accepted.');
    }
}
