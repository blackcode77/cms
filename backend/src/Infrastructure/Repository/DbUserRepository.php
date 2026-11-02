<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityHydratorInterface $entityHydrator,
        private ConnectionInterface $db,
    ) {}

    public function findByEmail(UserEmail $email): ?User
    {
        return $this->findOneBy(['email' => $email->toString()]);
    }

    public function findById(UserId $id): ?User
    {
        return $this->findOneBy(['id' => $id->toString()]);
    }

    public function hasByEmail(UserEmail $email): bool
    {
        return $this->db->createQuery()
            ->from(Table::USERS)
            ->where(['email' => $email->toString()])
            ->exists();
    }

    public function add(User $user): void
    {
        $this->db->createCommand()
            ->insert(Table::USERS, [
                'id' => $user->id->toString(),
                'email' => $user->email->toString(),
                'name' => $user->name,
                'password_hash' => $user->passwordHash,
                'role_id' => $user->roleId->toString(),
                'must_change_password' => $user->mustChangePassword,
                'created_at' => $user->createdAt->format('Y-m-d H:i:s.uP'),
            ])
            ->execute();
    }

    public function changePassword(UserId $id, string $passwordHash, bool $mustChangePassword): void
    {
        $this->db->createCommand()
            ->update(
                Table::USERS,
                ['password_hash' => $passwordHash, 'must_change_password' => $mustChangePassword],
                ['id' => $id->toString()],
            )
            ->execute();
    }

    /**
     * @param array<string, string> $condition
     */
    private function findOneBy(array $condition): ?User
    {
        /**
         * @var array{
         *     id: string, email: non-empty-string, name: string, password_hash: string,
         *     role_id: string, must_change_password: bool, created_at: string,
         * }|null $row
         */
        $row = $this->db->createQuery()
            ->from(Table::USERS)
            ->where($condition)
            ->one();
        if ($row === null) {
            return null;
        }

        return $this->entityHydrator->create(
            User::class,
            [
                'id' => UserId::fromString($row['id']),
                'email' => new UserEmail($row['email']),
                'name' => $row['name'],
                'passwordHash' => $row['password_hash'],
                'roleId' => RoleId::fromString($row['role_id']),
                'mustChangePassword' => $row['must_change_password'],
                'createdAt' => new DateTimeImmutable($row['created_at']),
            ],
        );
    }
}
