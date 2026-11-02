<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Value\UserEmail;
use App\Infrastructure\Table;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $db,
    ) {}

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
}
