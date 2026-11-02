<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Stringable;
use Webmozart\Assert\Assert;

/**
 * Dotted address of a Content element within a Page, e.g. `header.phone_number_one`.
 */
final readonly class ContentPath implements Stringable
{
    public const int LENGTH_LIMIT = 100;
    public const string PATTERN = '/^[a-z0-9_]+(?:\.[a-z0-9_]+)*$/';

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
