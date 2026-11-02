<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\Value\UserId;
use DateTimeImmutable;

interface TokenIssuerInterface
{
    public const int ACCESS_TTL_SECONDS = 900;

    /**
     * Signed access token that carries nothing but the User id and its validity window.
     */
    public function issueAccessToken(UserId $userId, DateTimeImmutable $now): string;

    /**
     * @return UserId|null null when the token is malformed, tampered with or expired at $now
     */
    public function userIdFromAccessToken(string $token, DateTimeImmutable $now): ?UserId;
}
