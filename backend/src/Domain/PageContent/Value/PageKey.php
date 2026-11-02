<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Stringable;
use Webmozart\Assert\Assert;

/**
 * Stable identifier of a Page, e.g. `home`, `site`.
 */
final readonly class PageKey implements Stringable
{
    public const int LENGTH_LIMIT = 50;
    public const string PATTERN = '/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/';

    /**
     * @param non-empty-string $value
     */
    public function __construct(
        private string $value,
    ) {
        Assert::maxLength($value, self::LENGTH_LIMIT);
        Assert::regex($value, self::PATTERN);
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
