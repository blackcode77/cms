<?php

declare(strict_types=1);

namespace App\Application\Auth;

/**
 * Freshly issued token pair; the refresh token is the plain opaque value (only its hash is stored).
 */
final readonly class Session
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken,
    ) {}
}
