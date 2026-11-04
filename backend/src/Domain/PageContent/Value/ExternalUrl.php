<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Value of a `url` element: an address of another website, `https://` only, with a dotted host
 * (`https://garant-intermodal.com/...`). Empty means "no link". Quotes, spaces and angle brackets are
 * rejected so the value is always safe inside an `href`.
 */
final readonly class ExternalUrl
{
    public const int LENGTH_LIMIT = 255;
    public const string PATTERN = '~^https://[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)*\.[a-z]{2,}(?::\d{1,5})?(?:[/?#][^\s"\'<>`]*)?$~i';

    public static function isValid(string $value): bool
    {
        return $value === '' || (strlen($value) <= self::LENGTH_LIMIT && preg_match(self::PATTERN, $value) === 1);
    }
}
