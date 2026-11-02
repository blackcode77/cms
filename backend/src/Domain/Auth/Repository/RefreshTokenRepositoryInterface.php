<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repository;

use App\Domain\Auth\Entity\RefreshToken;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;

interface RefreshTokenRepositoryInterface
{
    public function add(RefreshToken $token): void;

    /**
     * Revokes every refresh token of the User (ends all their Sessions).
     */
    public function revokeAllForUser(UserId $userId): void;

    /**
     * Atomically revokes an active (not revoked, not expired) token.
     *
     * @return UserId|null the token's owner, or null when the token was not active (used, revoked, expired, unknown)
     */
    public function revokeActive(string $tokenHash, DateTimeImmutable $now): ?UserId;
}
