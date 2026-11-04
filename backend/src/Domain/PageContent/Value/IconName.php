<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Name of an Icon in the site's icon collection, e.g. `map-pin`. The collection itself lives in the frontend.
 */
final readonly class IconName
{
    public const int LENGTH_LIMIT = 64;
    public const string PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public static function isValid(string $value): bool
    {
        return strlen($value) <= self::LENGTH_LIMIT && preg_match(self::PATTERN, $value) === 1;
    }
}
