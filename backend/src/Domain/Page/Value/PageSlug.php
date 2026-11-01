<?php

declare(strict_types=1);

namespace App\Domain\Page\Value;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class PageSlug implements Stringable
{
    public const int LENGTH_LIMIT = 100;
    public const string PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    /**
     * @param non-empty-string $value Lowercase latin words separated by single dashes.
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
