<?php

declare(strict_types=1);

namespace App\Application\Page\Exception;

use App\Domain\Page\Value\PageSlug;
use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class PageSlugAlreadyExistsException extends Exception
{
    public static function fromSlug(PageSlug $slug): self
    {
        return new self(sprintf('Page with slug "%s" already exists.', $slug));
    }
}
