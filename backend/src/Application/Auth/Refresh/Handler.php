<?php

declare(strict_types=1);

namespace App\Application\Auth\Refresh;

use App\Application\Auth\Session;
use App\Application\Auth\SessionIssuer;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokens,
        private SessionIssuer $sessionIssuer,
        private ClockInterface $clock,
    ) {}

    /**
     * Rotation: the presented token is revoked and a new pair issued; null when the token is not usable.
     */
    public function handle(string $refreshToken): ?Session
    {
        $userId = $this->refreshTokens->revokeActive(
            SessionIssuer::hash($refreshToken),
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );

        return $userId === null ? null : $this->sessionIssuer->issue($userId);
    }
}
