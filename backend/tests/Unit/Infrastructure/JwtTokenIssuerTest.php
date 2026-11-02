<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use App\Domain\User\Value\UserId;
use App\Infrastructure\JwtTokenIssuer;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use RuntimeException;

use function PHPUnit\Framework\assertTrue;

final class JwtTokenIssuerTest extends Unit
{
    public function testRefusesMissingShortAndPlaceholderSecrets(): void
    {
        foreach (['', 'too-short', 'change-me-to-a-long-random-string-0123456789'] as $secret) {
            try {
                new JwtTokenIssuer($secret)->issueAccessToken(new UserId(Uuid::uuid7()), new DateTimeImmutable());
                $this->fail('Expected RuntimeException for "' . $secret . '"');
            } catch (RuntimeException) {
            }
        }
    }

    public function testIssuesAndReadsTokensWithAStrongSecret(): void
    {
        $issuer = new JwtTokenIssuer(str_repeat('a1', 20));
        $id = new UserId(Uuid::uuid7());
        $now = new DateTimeImmutable();

        $read = $issuer->userIdFromAccessToken($issuer->issueAccessToken($id, $now), $now);

        assertTrue($read?->isEqualTo($id) ?? false);
    }
}
