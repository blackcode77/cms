<?php

declare(strict_types=1);

namespace App\Application\Auth\Login;

use App\Application\Auth\Exception\InvalidCredentialsException;
use App\Application\Auth\Exception\LoginLockedException;
use App\Application\Auth\Session;
use App\Application\Auth\SessionIssuer;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    /**
     * Argon2id hash of a throwaway password: verified when the email is unknown, so the response time does
     * not reveal whether the User exists.
     */
    private const string DUMMY_HASH = '$argon2id$v=19$m=65536,t=4,p=1$cmNuOVpQVUIzMmZELnkzWA$/ygLNMH7JwXD39qH+HYnat6xf9e6FDIN7RSloBCRwIM';

    public function __construct(
        private UserRepositoryInterface $users,
        private LoginAttemptRepositoryInterface $attempts,
        private PasswordHasherInterface $passwordHasher,
        private SessionIssuer $sessionIssuer,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws LoginLockedException
     * @throws InvalidCredentialsException
     */
    public function handle(Command $command): Session
    {
        $now = DateTimeImmutable::createFromInterface($this->clock->now());
        $email = $command->email->toString();

        if ($this->attempts->register($email, $command->ip, $now)->isLocked()) {
            throw LoginLockedException::create();
        }

        $user = $this->users->findByEmail($command->email);
        $valid = $this->passwordHasher->verify($command->password, $user === null ? self::DUMMY_HASH : $user->passwordHash);
        if ($user === null || !$valid) {
            throw InvalidCredentialsException::create();
        }

        $this->attempts->clear($email, $command->ip);

        return $this->sessionIssuer->issue($user->id);
    }
}
