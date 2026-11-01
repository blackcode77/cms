<?php

declare(strict_types=1);

namespace App\Domain\Page;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class PageTitle implements Stringable
{
    public const int LENGTH_LIMIT = 200;

    /**
     * @param non-empty-string $value
     */
    public function __construct(
        private string $value,
    ) {
        Assert::notEmpty($value);
        Assert::maxLength($value, self::LENGTH_LIMIT);
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
