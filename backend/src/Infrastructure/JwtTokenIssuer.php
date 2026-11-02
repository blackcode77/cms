<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Auth\TokenIssuerInterface;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use Throwable;

final readonly class JwtTokenIssuer implements TokenIssuerInterface
{
    private const string ALGORITHM = 'HS256';

    public function __construct(
        #[\SensitiveParameter]
        private string $secret,
    ) {}

    public function issueAccessToken(UserId $userId, DateTimeImmutable $now): string
    {
        $iat = $now->getTimestamp();

        return JWT::encode(
            ['sub' => $userId->toString(), 'iat' => $iat, 'exp' => $iat + self::ACCESS_TTL_SECONDS],
            $this->secret(),
            self::ALGORITHM,
        );
    }

    public function userIdFromAccessToken(string $token, DateTimeImmutable $now): ?UserId
    {
        $key = new Key($this->secret(), self::ALGORITHM);
        $previous = JWT::$timestamp;
        JWT::$timestamp = $now->getTimestamp();
        try {
            $payload = JWT::decode($token, $key);

            return isset($payload->sub) && is_string($payload->sub) ? UserId::fromString($payload->sub) : null;
        } catch (Throwable) {
            return null;
        } finally {
            JWT::$timestamp = $previous;
        }
    }

    private function secret(): string
    {
        return $this->secret !== '' ? $this->secret : throw new RuntimeException('JWT_SECRET is not configured.');
    }
}
