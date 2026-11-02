<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Auth\Entity\RefreshToken;
use App\Domain\Auth\Repository\RefreshTokenRepositoryInterface;
use App\Domain\User\Value\UserId;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private const string FORMAT = 'Y-m-d H:i:s.uP';

    public function __construct(
        private ConnectionInterface $db,
    ) {}

    public function add(RefreshToken $token): void
    {
        $this->db->createCommand()
            ->insert(Table::REFRESH_TOKENS, [
                'token_hash' => $token->tokenHash,
                'user_id' => $token->userId->toString(),
                'expires_at' => $token->expiresAt->format(self::FORMAT),
                'created_at' => $token->createdAt->format(self::FORMAT),
            ])
            ->execute();
    }

    /**
     * Removes every token of the User (they are also removed by the users FK cascade).
     */
    public function revokeAllForUser(UserId $userId): void
    {
        $this->db->createCommand()
            ->delete(Table::REFRESH_TOKENS, ['user_id' => $userId->toString()])
            ->execute();
    }

    public function revokeActive(string $tokenHash, DateTimeImmutable $now): ?UserId
    {
        /** @var list<array{user_id: string}> $rows */
        $rows = $this->db->createCommand(
            'UPDATE ' . Table::REFRESH_TOKENS . ' SET revoked_at = :now
             WHERE token_hash = :hash AND revoked_at IS NULL AND expires_at > :now
             RETURNING user_id',
            ['now' => $now->format(self::FORMAT), 'hash' => $tokenHash],
        )->queryAll();

        return $rows === [] ? null : UserId::fromString($rows[0]['user_id']);
    }
}
