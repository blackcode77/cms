<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

/**
 * Clients (identified by their normalized phone, ADR 0006) and their Orders, the Order request anti-spam log,
 * the generic per-minute rate limit counters, and the new Order/Client Permissions for the seeded Roles
 * (seeding is idempotent and never touches existing Roles, so existing databases get them here).
 */
final class M261105100000CreateClientsAndOrdersTables implements RevertibleMigrationInterface
{
    private const array NEW_PERMISSIONS = ['view_orders', 'manage_orders', 'handle_orders', 'view_clients', 'manage_clients'];

    public function up(MigrationBuilder $b): void
    {
        $b->execute("CREATE TABLE clients (
            id uuid PRIMARY KEY,
            phone varchar(16) NOT NULL UNIQUE,
            first_name varchar(100) NOT NULL DEFAULT '',
            last_name varchar(100) NOT NULL DEFAULT '',
            middle_name varchar(100) NOT NULL DEFAULT '',
            email varchar(255) NULL,
            comment text NOT NULL DEFAULT '',
            created_at timestamptz NOT NULL
        )");
        $b->execute('CREATE INDEX clients_created_at_idx ON clients (created_at)');

        $b->execute('CREATE SEQUENCE order_number_seq START 1000 MINVALUE 1000');
        $b->execute("CREATE TABLE orders (
            id uuid PRIMARY KEY,
            number integer NOT NULL UNIQUE,
            client_id uuid NOT NULL REFERENCES clients (id) ON DELETE CASCADE,
            status varchar(20) NOT NULL,
            manager_id uuid NULL REFERENCES users (id) ON DELETE SET NULL,
            cargo_from varchar(255) NOT NULL DEFAULT '',
            cargo_to varchar(255) NOT NULL DEFAULT '',
            cargo_type varchar(255) NOT NULL DEFAULT '',
            cargo_weight varchar(255) NOT NULL DEFAULT '',
            message text NOT NULL DEFAULT '',
            comment text NOT NULL DEFAULT '',
            created_at timestamptz NOT NULL,
            updated_at timestamptz NOT NULL
        )");
        $b->execute('ALTER SEQUENCE order_number_seq OWNED BY orders.number');
        $b->execute('CREATE INDEX orders_client_id_idx ON orders (client_id)');
        $b->execute('CREATE INDEX orders_manager_id_idx ON orders (manager_id)');
        $b->execute('CREATE INDEX orders_status_idx ON orders (status)');
        $b->execute('CREATE INDEX orders_created_at_idx ON orders (created_at)');

        $b->execute('CREATE TABLE order_request_log (
            ip varchar(45) NOT NULL,
            phone varchar(16) NOT NULL,
            created_at timestamptz NOT NULL
        )');
        $b->execute('CREATE INDEX order_request_log_ip_idx ON order_request_log (ip, created_at)');
        $b->execute('CREATE INDEX order_request_log_phone_idx ON order_request_log (phone, created_at)');

        $b->execute('CREATE TABLE rate_limit_hits (
            key varchar(255) PRIMARY KEY,
            window_start timestamptz NOT NULL,
            hits integer NOT NULL
        )');

        foreach (self::NEW_PERMISSIONS as $permission) {
            $b->execute(
                "INSERT INTO role_permissions (role_id, permission)
                 SELECT id, '$permission' FROM roles WHERE name IN ('admin', 'manager')
                 ON CONFLICT DO NOTHING",
            );
        }
    }

    public function down(MigrationBuilder $b): void
    {
        $b->execute("DELETE FROM role_permissions WHERE permission IN ('" . implode("', '", self::NEW_PERMISSIONS) . "')");
        $b->dropTable('rate_limit_hits');
        $b->dropTable('order_request_log');
        $b->dropTable('orders');
        $b->dropTable('clients');
    }
}
