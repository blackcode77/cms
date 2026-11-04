<?php

declare(strict_types=1);

namespace App\Domain\Client\Value;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class ClientEmail implements Stringable
{
    public const int LENGTH_LIMIT = 255;

    private string $value;

    public function __construct(string $value)
    {
        $value = mb_strtolower(trim($value));
        Assert::notEmpty($value);
        Assert::maxLength($value, self::LENGTH_LIMIT);
        Assert::email($value);
        $this->value = $value;
    }

    /**
     * Null for an empty input: the email of a Client is optional.
     */
    public static function fromOptional(string $value): ?self
    {
        return trim($value) === '' ? null : new self($value);
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
