<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidIconNameException extends Exception
{
    public static function create(): self
    {
        return new self('Icon must be a name from the icon collection, e.g. "map-pin".');
    }
}
