<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidUrlException extends Exception
{
    public static function create(): self
    {
        return new self('Website address must start with https://, e.g. "https://garant-intermodal.com".');
    }
}
