<?php

declare(strict_types=1);

namespace App\Domain\Auth\Entity;

use App\Domain\User\Value\UserId;
use DateTimeImmutable;

/**
 * Server-side record of an issued refresh token. Only the hash of the opaque token is kept.
 */
final class RefreshToken
{
    public readonly UserId $userId;
    public readonly string $tokenHash;
    public readonly DateTimeImmutable $expiresAt;
    public readonly DateTimeImmutable $createdAt;

    public function __construct(
        UserId $userId,
        string $tokenHash,
        DateTimeImmutable $expiresAt,
        DateTimeImmutable $createdAt,
    ) {
        $this->userId = $userId;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
        $this->createdAt = $createdAt;
    }
}
