<?php

declare(strict_types=1);

namespace App\Application\Auth\ChangePassword;

use App\Application\Auth\Exception\InvalidCurrentPasswordException;
use App\Application\Auth\Exception\LoginLockedException;
use App\Application\Auth\Exception\PasswordUnchangedException;
use App\Application\Auth\Session;
use App\Application\Auth\SessionIssuer;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $passwordHasher,
        private LoginAttemptRepositoryInterface $attempts,
        private RefreshTokenRepositoryInterface $refreshTokens,
        private SessionIssuer $sessionIssuer,
        private ClockInterface $clock,
    ) {}

    /**
     * Ends all the User's Sessions and returns a fresh one for the caller. Wrong current passwords count toward
     * the same attempt limit as sign-in (under a separate key).
     *
     * @throws InvalidCurrentPasswordException
     * @throws PasswordUnchangedException
     * @throws LoginLockedException
     */
    public function handle(User $user, string $currentPassword, string $newPassword, string $ip): Session
    {
        $now = DateTimeImmutable::createFromInterface($this->clock->now());
        $key = 'password-change:' . $user->id->toString();

        if ($this->attempts->register($key, $ip, $now)->isLocked()) {
            throw LoginLockedException::forPasswordChange();
        }
        if (!$this->passwordHasher->verify($currentPassword, $user->passwordHash)) {
            throw InvalidCurrentPasswordException::create();
        }
        $this->attempts->clear($key, $ip);

        if ($newPassword === $currentPassword) {
            throw PasswordUnchangedException::create();
        }

        $this->users->changePassword($user->id, $this->passwordHasher->hash($newPassword), false);
        $this->refreshTokens->revokeAllForUser($user->id, $now);

        return $this->sessionIssuer->issue($user->id);
    }
}
