<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Application\Shared\MailerInterface;
use App\Application\Shared\PasswordGenerator;
use App\Application\Shared\PasswordHasherInterface;
use App\Application\User\AdminGuard;
use App\Application\User\Exception\AdminRequiredException;
use App\Application\User\Exception\RoleNotAssignableException;
use App\Application\User\Exception\UserEmailAlreadyExistsException;
use App\Domain\Role\Repository\RoleRepositoryInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserId;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

final readonly class Handler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository,
        private PasswordHasherInterface $passwordHasher,
        private PasswordGenerator $passwordGenerator,
        private MailerInterface $mailer,
        private ClockInterface $clock,
        private AdminGuard $adminGuard,
    ) {}

    /**
     * The User is stored even when the credentials mail cannot be sent ({@see Result::$mailSent} tells).
     *
     * @throws UserEmailAlreadyExistsException
     * @throws RoleNotAssignableException
     * @throws AdminRequiredException
     */
    public function handle(Command $command): Result
    {
        $role = $this->roleRepository->findById($command->roleId);
        if ($role === null || $role->isVisiter()) {
            throw RoleNotAssignableException::create();
        }
        $this->adminGuard->assertMayAssign($command->actorId, $role);
        if ($this->userRepository->hasByEmail($command->email)) {
            throw UserEmailAlreadyExistsException::fromEmail($command->email);
        }

        $password = $this->passwordGenerator->generate();
        $id = new UserId(Uuid::uuid7());

        $this->userRepository->add(
            new User(
                $id,
                $command->email,
                $command->name,
                $this->passwordHasher->hash($password),
                $role->id,
                true,
                DateTimeImmutable::createFromInterface($this->clock->now()),
            ),
        );

        try {
            $this->mailer->send(
                Mail::credentials($command->email->toString(), $password, 'Your CMS account'),
            );
            $mailSent = true;
        } catch (MailDeliveryException) {
            $mailSent = false;
        }

        return new Result($id, $mailSent);
    }
}
