<?php

declare(strict_types=1);

namespace App\Application\Auth\Login;

use App\Application\Auth\Exception\InvalidCredentialsException;
use App\Application\Auth\Exception\LoginLockedException;
use App\Application\Auth\Session;
use App\Application\Auth\SessionIssuer;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
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

        $attempt = $this->attempts->find($email, $command->ip);
        if ($attempt !== null && $attempt->isLocked($now)) {
            throw LoginLockedException::create();
        }

        $user = $this->users->findByEmail($command->email);
        if ($user === null || !$this->passwordHasher->verify($command->password, $user->passwordHash)) {
            $attempt ??= new LoginAttempt($email, $command->ip);
            $attempt->registerFailure($now);
            $this->attempts->save($attempt);

            throw InvalidCredentialsException::create();
        }

        if ($attempt !== null) {
            $this->attempts->clear($email, $command->ip);
        }

        return $this->sessionIssuer->issue($user->id);
    }
}
