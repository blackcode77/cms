<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

/**
 * Attempts are now counted before the password is checked inside a fixed window, so the column is the
 * end of that window (rows past it are purged).
 */
final class M251103100000RenameLoginAttemptWindow implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $b->execute('TRUNCATE TABLE login_attempts');
        $b->execute('ALTER TABLE login_attempts RENAME COLUMN locked_until TO window_until');
        $b->execute('ALTER TABLE login_attempts ALTER COLUMN window_until SET NOT NULL');
        $b->execute('CREATE INDEX login_attempts_window_until_idx ON login_attempts (window_until)');
    }

    public function down(MigrationBuilder $b): void
    {
        $b->execute('DROP INDEX login_attempts_window_until_idx');
        $b->execute('ALTER TABLE login_attempts ALTER COLUMN window_until DROP NOT NULL');
        $b->execute('ALTER TABLE login_attempts RENAME COLUMN window_until TO locked_until');
    }
}
