<?php

declare(strict_types=1);

namespace App\Application\PageContent\Exception;

use Exception;
use Yiisoft\ErrorHandler\Exception\UserException;

#[UserException]
final class InvalidRouteStopsException extends Exception
{
    public static function create(): self
    {
        return new self('A Route needs 2-20 Stops, each with x and y from 0 to 100, a label of 1-60 characters (at most 2 lines) and a label side: top, right, bottom or left.');
    }
}
