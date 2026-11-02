<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Auth\Entity\RefreshToken;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\User\Value\UserId;
use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class SessionIssuer
{
    public const int REFRESH_TTL_SECONDS = 604800;

    public function __construct(
        private TokenIssuerInterface $tokenIssuer,
        private RefreshTokenRepositoryInterface $refreshTokens,
        private ClockInterface $clock,
    ) {}

    public function issue(UserId $userId): Session
    {
        $now = DateTimeImmutable::createFromInterface($this->clock->now());
        $plain = bin2hex(random_bytes(32));
        $this->refreshTokens->add(
            new RefreshToken(
                $userId,
                self::hash($plain),
                $now->add(new DateInterval('PT' . self::REFRESH_TTL_SECONDS . 'S')),
                $now,
            ),
        );

        return new Session($this->tokenIssuer->issueAccessToken($userId, $now), $plain);
    }

    public static function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
