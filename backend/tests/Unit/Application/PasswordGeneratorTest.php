<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application;

use App\Application\Shared\PasswordGenerator;
use Codeception\Test\Unit;

final class PasswordGeneratorTest extends Unit
{
    public function testGeneratesSixteenCharacterAlphanumericPasswords(): void
    {
        $generator = new PasswordGenerator();

        $passwords = [];
        for ($i = 0; $i < 20; $i++) {
            $password = $generator->generate();
            $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{16}$/', $password);
            $passwords[] = $password;
        }

        $this->assertCount(20, array_unique($passwords));
    }
}
