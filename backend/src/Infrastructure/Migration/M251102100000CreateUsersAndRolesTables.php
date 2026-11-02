<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M251102100000CreateUsersAndRolesTables implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $b->execute('CREATE TABLE roles (
            id uuid PRIMARY KEY,
            name varchar(100) NOT NULL UNIQUE
        )');

        $b->execute('CREATE TABLE role_permissions (
            role_id uuid NOT NULL REFERENCES roles (id) ON DELETE CASCADE,
            permission varchar(50) NOT NULL,
            PRIMARY KEY (role_id, permission)
        )');

        $b->execute('CREATE TABLE users (
            id uuid PRIMARY KEY,
            email varchar(255) NOT NULL UNIQUE,
            name varchar(200) NOT NULL,
            password_hash varchar(255) NOT NULL,
            role_id uuid NOT NULL REFERENCES roles (id),
            must_change_password boolean NOT NULL DEFAULT false,
            created_at timestamptz NOT NULL
        )');
    }

    public function down(MigrationBuilder $b): void
    {
        $b->dropTable('users');
        $b->dropTable('role_permissions');
        $b->dropTable('roles');
    }
}
