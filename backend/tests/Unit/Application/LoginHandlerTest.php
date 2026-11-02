<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application;

use App\Application\Auth\Exception\InvalidCredentialsException;
use App\Application\Auth\Login\Command;
use App\Application\Auth\Login\Handler;
use App\Application\Auth\SessionIssuer;
use App\Application\Auth\TokenIssuerInterface;
use App\Application\Shared\PasswordHasherInterface;
use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\Entity\RefreshToken;
use App\Domain\Auth\Repository\LoginAttemptRepositoryInterface;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\Role\Value\RoleId;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;
use App\Infrastructure\SystemClock;
use Codeception\Test\Unit;
use DateTimeImmutable;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertStringStartsWith;

final class LoginHandlerTest extends Unit
{
    public function testUnknownEmailStillVerifiesAPasswordHash(): void
    {
        $hasher = new class implements PasswordHasherInterface {
            /** @var list<string> */
            public array $verifiedAgainst = [];

            public function hash(string $plain): string
            {
                return 'hashed:' . $plain;
            }

            public function verify(string $plain, string $hash): bool
            {
                $this->verifiedAgainst[] = $hash;

                return false;
            }
        };
        $attempts = new class implements LoginAttemptRepositoryInterface {
            public function register(string $key, string $ip, DateTimeImmutable $now): LoginAttempt
            {
                return new LoginAttempt($key, $ip, 1, $now);
            }

            public function clear(string $key, string $ip): void {}
        };
        $tokens = new class implements RefreshTokenRepositoryInterface {
            public function add(RefreshToken $token): void {}

            public function revokeAllForUser(UserId $userId, DateTimeImmutable $now): void {}

            public function revokeActive(string $tokenHash, DateTimeImmutable $now): ?UserId
            {
                return null;
            }
        };
        $tokenIssuer = new class implements TokenIssuerInterface {
            public function issueAccessToken(UserId $userId, DateTimeImmutable $now): string
            {
                return 'x';
            }

            public function userIdFromAccessToken(string $token, DateTimeImmutable $now): ?UserId
            {
                return null;
            }
        };
        $clock = new SystemClock();
        $handler = new Handler(
            new EmptyUserRepository(),
            $attempts,
            $hasher,
            new SessionIssuer($tokenIssuer, $tokens, $clock),
            $clock,
        );

        try {
            $handler->handle(new Command(new UserEmail('ghost@example.com'), 'whatever', '10.0.0.1'));
            $this->fail('Expected InvalidCredentialsException');
        } catch (InvalidCredentialsException) {
        }

        assertCount(1, $hasher->verifiedAgainst);
        assertStringStartsWith('$argon2id$', $hasher->verifiedAgainst[0]);
    }
}

final class EmptyUserRepository implements UserRepositoryInterface
{
    public function findByEmail(UserEmail $email): ?User
    {
        return null;
    }

    public function findById(UserId $id): ?User
    {
        return null;
    }

    public function hasByEmail(UserEmail $email): bool
    {
        return false;
    }

    public function add(User $user): void {}

    public function update(User $user): void {}

    public function remove(UserId $id): void {}

    public function countByRole(RoleId $roleId): int
    {
        return 0;
    }

    public function changePassword(UserId $id, string $passwordHash, bool $mustChangePassword): void {}
}
