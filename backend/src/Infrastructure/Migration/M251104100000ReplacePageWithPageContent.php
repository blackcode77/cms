<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

/**
 * The title/slug/content `page` table is replaced by per-page JSON of edited Content element values.
 * `content` is flat: keys are dotted element paths, values are `{type, value, enabled}`.
 */
final class M251104100000ReplacePageWithPageContent implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $b->execute('DROP TABLE page');
        $b->execute("CREATE TABLE page_content (
            page_key varchar(50) PRIMARY KEY,
            content jsonb NOT NULL DEFAULT '{}'::jsonb,
            updated_at timestamptz NOT NULL
        )");
    }

    public function down(MigrationBuilder $b): void
    {
        $b->execute('DROP TABLE page_content');
        $b->execute('CREATE TABLE page (
            id uuid PRIMARY KEY,
            title varchar(200) NOT NULL,
            slug varchar(100) NOT NULL UNIQUE,
            content text NOT NULL,
            created_at timestamptz NOT NULL
        )');
    }
}
