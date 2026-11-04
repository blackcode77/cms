<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidLinkException extends Exception
{
    public static function create(): self
    {
        return new self('Link must be a path inside the site, e.g. "/services" or "/services#agent".');
    }
}
