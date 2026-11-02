<?php

declare(strict_types=1);

namespace App\Application\Auth\Logout;

use App\Application\Auth\SessionIssuer;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokens,
        private ClockInterface $clock,
    ) {}

    public function handle(string $refreshToken): void
    {
        $this->refreshTokens->revokeActive(
            SessionIssuer::hash($refreshToken),
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );
    }
}
