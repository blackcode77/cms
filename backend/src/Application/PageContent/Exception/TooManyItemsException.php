<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use App\Domain\PageContent\Value\ContentList;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class TooManyItemsException extends Exception
{
    public static function create(): self
    {
        return new self(sprintf('A list must not have more than %d items.', ContentList::MAX_ITEMS));
    }
}
