<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidDocumentUrlException extends Exception
{
    public static function create(): self
    {
        return new self('A file must be a document uploaded to the site.');
    }
}
