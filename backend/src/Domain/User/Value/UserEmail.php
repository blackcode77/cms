<?php

declare(strict_types=1);

namespace App\Domain\User\Value;

use Stringable;
use Webmozart\Assert\Assert;

final readonly class UserEmail implements Stringable
{
    public const int LENGTH_LIMIT = 255;

    private string $value;

    /**
     * The email is the login, so it is normalized to lower case.
     */
    public function __construct(string $value)
    {
        $value = mb_strtolower(trim($value));
        Assert::notEmpty($value);
        Assert::maxLength($value, self::LENGTH_LIMIT);
        Assert::email($value);
        $this->value = $value;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        /** @var non-empty-string */
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
