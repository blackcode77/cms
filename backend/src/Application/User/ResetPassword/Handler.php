<?php

declare(strict_types=1);

namespace App\Application\User\ResetPassword;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Application\Shared\MailerInterface;
use App\Application\Shared\PasswordGenerator;
use App\Application\Shared\PasswordHasherInterface;
use App\Application\User\AdminGuard;
use App\Application\User\Exception\AdminRequiredException;
use App\Application\User\Exception\UserNotFoundException;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RefreshTokenRepositoryInterface $refreshTokens,
        private PasswordHasherInterface $passwordHasher,
        private PasswordGenerator $passwordGenerator,
        private MailerInterface $mailer,
        private ClockInterface $clock,
        private AdminGuard $adminGuard,
    ) {}

    /**
     * Generates a new password, forces its change on next sign-in, ends the User's Sessions and emails it.
     * The new password is stored even when the mail cannot be sent (the result tells).
     *
     * @return bool whether the mail was sent
     *
     * @throws UserNotFoundException
     * @throws AdminRequiredException
     */
    public function handle(UserId $id, UserId $actorId): bool
    {
        $user = $this->userRepository->findById($id) ?? throw UserNotFoundException::create();
        $this->adminGuard->assertMayManage($actorId, $user);

        $password = $this->passwordGenerator->generate();
        $this->userRepository->changePassword($id, $this->passwordHasher->hash($password), true);
        $this->refreshTokens->revokeAllForUser($id, DateTimeImmutable::createFromInterface($this->clock->now()));

        try {
            $this->mailer->send(Mail::credentials($user->email->toString(), $password, 'Your new CMS password'));

            return true;
        } catch (MailDeliveryException) {
            return false;
        }
    }
}
