<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Value of a Link: a path inside the public site, optionally with an anchor (`/services#agent`).
 * Empty means "not clickable". No scheme or host, so `javascript:` and other sites cannot get in.
 */
final readonly class LinkPath
{
    public const int LENGTH_LIMIT = 255;
    public const string PATTERN = '~^/(?!/)[a-z0-9/_-]*(?:#[a-z0-9_-]+)?$~';

    public static function isValid(string $value): bool
    {
        return $value === '' || (strlen($value) <= self::LENGTH_LIMIT && preg_match(self::PATTERN, $value) === 1);
    }
}
