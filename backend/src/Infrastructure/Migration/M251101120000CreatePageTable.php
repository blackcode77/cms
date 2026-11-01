<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M251101120000CreatePageTable implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $b->execute('CREATE TABLE page (
            id uuid PRIMARY KEY,
            title varchar(200) NOT NULL,
            slug varchar(100) NOT NULL UNIQUE,
            content text NOT NULL,
            created_at timestamptz NOT NULL
        )');
    }

    public function down(MigrationBuilder $b): void
    {
        $b->dropTable('page');
    }
}
