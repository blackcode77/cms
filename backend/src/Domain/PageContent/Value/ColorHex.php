<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Value of a `color` field: `#rrggbb`, stored lowercase (e.g. the line colour of a Route).
 */
final readonly class ColorHex
{
    public const string PATTERN = '/^#[0-9a-f]{6}$/i';

    /**
     * The canonical (lowercase) colour, or `null` when the value is not `#rrggbb`.
     */
    public static function normalize(string $value): ?string
    {
        return preg_match(self::PATTERN, $value) === 1 ? strtolower($value) : null;
    }
}
