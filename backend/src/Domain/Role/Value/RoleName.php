<?php

declare(strict_types=1);

namespace App\Domain\Role\Value;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class RoleName implements Stringable
{
    public const string ADMIN = 'admin';
    public const string MANAGER = 'manager';
    public const string VISITER = 'visiter';

    public const int LENGTH_LIMIT = 100;

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
