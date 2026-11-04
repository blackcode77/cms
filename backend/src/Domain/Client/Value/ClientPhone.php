<?php

declare(strict_types=1);

namespace App\Domain\Client\Value;

use InvalidArgumentException;
use Stringable;

/**
 * The phone number that identifies a Client, normalized to E.164 (`+` and 8–15 digits), so differently typed
 * forms of one number are the same Client. A number without a country code is read as Russian:
 * `8XXXXXXXXXX` and bare ten digits become `+7XXXXXXXXXX`.
 */
final readonly class ClientPhone implements Stringable
{
    public const int LENGTH_LIMIT = 16;

    private string $value;

    /**
     * @throws InvalidArgumentException when the input is not a phone number
     */
    public function __construct(string $input)
    {
        $value = self::normalize($input);
        if ($value === null) {
            throw new InvalidArgumentException('Invalid phone number.');
        }
        $this->value = $value;
    }

    public static function tryFrom(string $input): ?self
    {
        return self::normalize($input) === null ? null : new self($input);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $input): ?string
    {
        $input = trim($input);
        // only digits and the usual separators may appear; letters mean it is not a phone number
        if ($input === '' || preg_match('/^\+?[\d\s()\-.]+$/', $input) !== 1) {
            return null;
        }
        $digits = (string) preg_replace('/\D/', '', $input);

        if (str_starts_with($input, '+')) {
            return preg_match('/^[1-9]\d{7,14}$/', $digits) === 1 ? '+' . $digits : null;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            return '+7' . substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            return '+7' . $digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '7')) {
            return '+' . $digits;
        }

        return null;
    }
}
