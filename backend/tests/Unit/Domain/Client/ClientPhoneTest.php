<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Client;

use App\Domain\Client\Value\ClientPhone;
use Codeception\Test\Unit;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class ClientPhoneTest extends Unit
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function validNumbers(): iterable
    {
        yield 'russian with +7' => ['+7 (999) 123-45-67', '+79991234567'];
        yield 'russian with 8' => ['8 999 123 45 67', '+79991234567'];
        yield 'russian with 7, no plus' => ['79991234567', '+79991234567'];
        yield 'ten digits' => ['9991234567', '+79991234567'];
        yield 'chinese' => ['+86 138 0013 8000', '+8613800138000'];
        yield 'dots' => ['+49.30.1234567', '+49301234567'];
    }

    /**
     * @dataProvider validNumbers
     */
    public function testNormalizesToE164(string $input, string $expected): void
    {
        assertSame($expected, new ClientPhone($input)->toString());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidNumbers(): iterable
    {
        yield 'empty' => [''];
        yield 'letters' => ['+7 999 ABC 45 67'];
        yield 'too short' => ['12345'];
        yield 'too long' => ['+1234567890123456'];
        yield 'nine digits without code' => ['999123456'];
        yield 'leading zero country code' => ['+0123456789'];
    }

    /**
     * @dataProvider invalidNumbers
     */
    public function testRejectsWhatIsNotAPhone(string $input): void
    {
        assertNull(ClientPhone::tryFrom($input));
    }
}
