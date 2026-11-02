<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Stringable;
use Webmozart\Assert\Assert;

/**
 * Where an `image` Content element may point: our own uploads or the bundled static images.
 */
final readonly class ImageUrl implements Stringable
{
    public const string PATTERN = '#^/(?:uploads|img)/[A-Za-z0-9_\-]+(?:[./][A-Za-z0-9_\-]+)*$#';

    public function __construct(
        private string $value,
    ) {
        Assert::regex($value, self::PATTERN);
        Assert::notContains($value, '..');
    }

    public static function isValid(string $value): bool
    {
        return preg_match(self::PATTERN, $value) === 1 && !str_contains($value, '..');
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
