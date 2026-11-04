<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Tests\Support\ConsoleTester;
use PDO;

use function dirname;

final class SeedCest
{
    public function _before(ConsoleTester $I): void
    {
        $this->pdo()->exec('TRUNCATE TABLE users, role_permissions, roles CASCADE');
        putenv('ADMIN_EMAIL=Owner@Example.com');
        putenv('ADMIN_PASSWORD=first-password');
    }

    public function _after(ConsoleTester $I): void
    {
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
    }

    public function seedsDefaultRolesAndInitialAdmin(ConsoleTester $I): void
    {
        $I->runApp('app:seed');
        $I->canSeeResultCodeIs(0);

        $pdo = $this->pdo();
        $roles = $pdo->query('SELECT name FROM roles ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);
        $I->assertSame(['admin', 'manager', 'visiter'], $roles);

        $perms = static fn(string $role): array => $pdo->query(
            "SELECT permission FROM role_permissions rp JOIN roles r ON r.id = rp.role_id
             WHERE r.name = '$role' ORDER BY permission",
        )->fetchAll(PDO::FETCH_COLUMN);
        $I->assertSame(
            [
                'edit_page', 'handle_orders', 'manage_clients', 'manage_orders', 'manage_roles', 'manage_users',
                'view_clients', 'view_dashboard', 'view_orders', 'view_roles', 'view_users',
            ],
            $perms('admin'),
        );
        $I->assertSame(
            ['edit_page', 'handle_orders', 'manage_clients', 'manage_orders', 'view_clients', 'view_dashboard', 'view_orders'],
            $perms('manager'),
        );
        $I->assertSame([], $perms('visiter'));

        $user = $pdo->query(
            'SELECT u.email, u.password_hash, r.name AS role FROM users u JOIN roles r ON r.id = u.role_id',
        )->fetchAll(PDO::FETCH_ASSOC);
        $I->assertCount(1, $user);
        $I->assertSame('owner@example.com', $user[0]['email']);
        $I->assertSame('admin', $user[0]['role']);
        $I->assertStringStartsWith('$argon2id$', $user[0]['password_hash']);
        $I->assertTrue(password_verify('first-password', $user[0]['password_hash']));
    }

    public function isIdempotentAndKeepsExistingAdmin(ConsoleTester $I): void
    {
        $I->runApp('app:seed');
        $hash = $this->pdo()->query('SELECT password_hash FROM users')->fetchColumn();

        putenv('ADMIN_PASSWORD=changed-password');
        $I->runApp('app:seed');
        $I->canSeeResultCodeIs(0);

        $pdo = $this->pdo();
        $I->assertSame(3, (int) $pdo->query('SELECT COUNT(*) FROM roles')->fetchColumn());
        $I->assertSame(18, (int) $pdo->query('SELECT COUNT(*) FROM role_permissions')->fetchColumn());
        $I->assertSame(1, (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn());
        $I->assertSame($hash, $pdo->query('SELECT password_hash FROM users')->fetchColumn());
    }

    public function failsWithoutAdminCredentials(ConsoleTester $I): void
    {
        putenv('ADMIN_PASSWORD=');
        $I->runShellCommand(dirname(__DIR__, 2) . '/yii app:seed', false);
        $I->canSeeResultCodeIs(78);
    }

    private function pdo(): PDO
    {
        return new PDO(
            sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                getenv('DB_HOST') ?: 'postgres',
                getenv('DB_PORT') ?: '5432',
                getenv('DB_TEST_NAME') ?: 'app_test',
            ),
            getenv('DB_USER') ?: 'app',
            getenv('DB_PASSWORD') ?: 'app',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
    }
}
