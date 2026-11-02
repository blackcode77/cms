<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use App\Domain\PageContent\Value\ContentType;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class ContentValueTooLongException extends Exception
{
    public static function forType(ContentType $type): self
    {
        return new self(sprintf('Value of type "%s" must not exceed %d characters.', $type->value, $type->maxLength()));
    }
}
