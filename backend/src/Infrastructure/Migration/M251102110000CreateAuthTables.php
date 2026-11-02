<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M251102110000CreateAuthTables implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $b->execute('CREATE TABLE refresh_tokens (
            token_hash varchar(64) PRIMARY KEY,
            user_id uuid NOT NULL REFERENCES users (id) ON DELETE CASCADE,
            expires_at timestamptz NOT NULL,
            created_at timestamptz NOT NULL,
            revoked_at timestamptz NULL
        )');
        $b->execute('CREATE INDEX refresh_tokens_user_id_idx ON refresh_tokens (user_id)');

        $b->execute('CREATE TABLE login_attempts (
            email varchar(255) NOT NULL,
            ip varchar(45) NOT NULL,
            failures integer NOT NULL,
            locked_until timestamptz NULL,
            PRIMARY KEY (email, ip)
        )');
    }

    public function down(MigrationBuilder $b): void
    {
        $b->dropTable('login_attempts');
        $b->dropTable('refresh_tokens');
    }
}
