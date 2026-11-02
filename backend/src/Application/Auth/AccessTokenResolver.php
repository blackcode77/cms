<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Turns the access token of a request into the signed-in User and Role (fresh from the database, so changed
 * Permissions apply immediately). Reused by the authentication middleware.
 */
final readonly class AccessTokenResolver
{
    public function __construct(
        private TokenIssuerInterface $tokenIssuer,
        private UserRepositoryInterface $users,
        private RoleRepositoryInterface $roles,
        private ClockInterface $clock,
    ) {}

    public function resolve(?string $accessToken): ?CurrentUser
    {
        if ($accessToken === null || $accessToken === '') {
            return null;
        }
        $userId = $this->tokenIssuer->userIdFromAccessToken(
            $accessToken,
            DateTimeImmutable::createFromInterface($this->clock->now()),
        );
        $user = $userId === null ? null : $this->users->findById($userId);
        $role = $user === null ? null : $this->roles->findById($user->roleId);

        return $user === null || $role === null ? null : new CurrentUser($user, $role);
    }
}
