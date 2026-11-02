<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Environment;
use App\Infrastructure\ShiftableClock;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\StreamFactory;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Yii\Runner\Http\HttpApplicationRunner;

use function dirname;
use function json_decode;
use function json_encode;

/**
 * Runs the API application in-process (no HTTP server) against the `app_test` database.
 */
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    /**
     * @param array<string, string> $cookies
     * @param array<string, string> $serverParams e.g. ['REMOTE_ADDR' => '10.0.0.1']
     */
    public function sendApiRequest(
        string $method,
        string $uri,
        ?array $json = null,
        array $cookies = [],
        array $serverParams = [],
    ): ResponseInterface {
        $request = new ServerRequest(method: $method, uri: $uri, serverParams: $serverParams)
            ->withCookieParams($cookies);
        if ($json !== null) {
            $request = $request
                ->withHeader('Content-Type', 'application/json')
                ->withBody(new StreamFactory()->createStream(json_encode($json, JSON_THROW_ON_ERROR)));
        }

        $runner = new HttpApplicationRunner(
            rootPath: dirname(__DIR__, 2),
            environment: Environment::appEnv(),
            bootstrapGroup: 'bootstrap-api',
            eventsGroup: 'events-api',
            diGroup: 'di-api',
            diProvidersGroup: 'di-providers-api',
            diDelegatesGroup: 'di-delegates-api',
            diTagsGroup: 'di-tags-api',
            paramsGroup: 'params-api',
            nestedParamsGroups: ['params', 'params-web'],
            nestedEventsGroups: ['events', 'events-web'],
        );

        $response = $runner->runAndGetResponse($request);
        $this->dropLeakedAppConnections();
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public function decode(ResponseInterface $response): array
    {
        /** @var array<string, mixed> */
        return json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
    }

    public function cleanDatabase(): void
    {
        ShiftableClock::reset();
        $this->pdo()->exec(
            'TRUNCATE TABLE page, refresh_tokens, login_attempts, users, role_permissions, roles CASCADE',
        );
    }

    /**
     * Moves the application clock, e.g. '+16 minutes'. Undone by cleanDatabase().
     */
    public function travelTo(string $modifier): void
    {
        ShiftableClock::set(new DateTimeImmutable($modifier));
    }

    /**
     * Inserts a User (and its Role with default Permissions when missing) straight into the database.
     */
    public function haveUser(
        string $email,
        string $password,
        string $role = 'manager',
        bool $mustChangePassword = false,
    ): void {
        $permissions = [
            'admin' => ['view_dashboard', 'edit_page', 'view_users', 'manage_users', 'view_roles', 'manage_roles'],
            'manager' => ['view_dashboard', 'edit_page'],
        ][$role] ?? [];

        $pdo = $this->pdo();
        $roleId = $pdo->query("SELECT id FROM roles WHERE name = '$role'")->fetchColumn();
        if ($roleId === false) {
            $roleId = Uuid::uuid7()->toString();
            $pdo->prepare('INSERT INTO roles (id, name) VALUES (?, ?)')->execute([$roleId, $role]);
            foreach ($permissions as $permission) {
                $pdo->prepare('INSERT INTO role_permissions (role_id, permission) VALUES (?, ?)')
                    ->execute([$roleId, $permission]);
            }
        }
        $pdo->prepare(
            'INSERT INTO users (id, email, name, password_hash, role_id, must_change_password, created_at)
             VALUES (?, ?, ?, ?, ?, ?, now())',
        )->execute([
            Uuid::uuid7()->toString(),
            $email,
            'Test User',
            password_hash($password, PASSWORD_ARGON2ID),
            $roleId,
            $mustChangePassword ? 'true' : 'false',
        ]);
    }

    /**
     * Cookies set by the response: name => attributes (the cookie value is under key 'value').
     *
     * @return array<string, array<string, string|true>>
     */
    public function setCookies(ResponseInterface $response): array
    {
        $result = [];
        foreach ($response->getHeader('Set-Cookie') as $header) {
            $parts = array_map(trim(...), explode(';', $header));
            [$name, $value] = explode('=', array_shift($parts), 2);
            $attributes = ['value' => $value];
            foreach ($parts as $part) {
                [$key, $val] = array_pad(explode('=', $part, 2), 2, true);
                $attributes[strtolower($key)] = $val;
            }
            $result[$name] = $attributes;
        }

        return $result;
    }

    /**
     * @return array<string, string> name => value, ready to be sent back as request cookies
     */
    public function cookieValues(ResponseInterface $response): array
    {
        return array_map(static fn(array $a): string => (string) $a['value'], $this->setCookies($response));
    }

    /**
     * Logs in and returns the Session cookies.
     *
     * @return array<string, string>
     */
    public function loginAs(string $email, string $password): array
    {
        return $this->cookieValues(
            $this->sendApiRequest('POST', '/api/v1/auth/login', ['email' => $email, 'password' => $password]),
        );
    }

    /**
     * Creates a User with the given Role and returns their Session cookies.
     *
     * @return array<string, string>
     */
    public function haveSessionFor(string $role = 'manager'): array
    {
        $email = $role . '@example.com';
        $this->haveUser($email, 'secret-pass', $role);

        return $this->loginAs($email, 'secret-pass');
    }

    /**
     * Inserts a Role with exactly the given Permissions unless a Role with that name exists.
     *
     * @param list<string> $permissions
     */
    public function haveRole(string $name, array $permissions = []): void
    {
        if ($this->roleId($name) !== '') {
            return;
        }
        $pdo = $this->pdo();
        $roleId = Uuid::uuid7()->toString();
        $pdo->prepare('INSERT INTO roles (id, name) VALUES (?, ?)')->execute([$roleId, $name]);
        foreach ($permissions as $permission) {
            $pdo->prepare('INSERT INTO role_permissions (role_id, permission) VALUES (?, ?)')
                ->execute([$roleId, $permission]);
        }
    }

    public function roleId(string $name): string
    {
        return (string) $this->pdo()->query("SELECT id FROM roles WHERE name = '$name'")->fetchColumn();
    }

    public function countRows(string $table): int
    {
        return (int) $this->pdo()->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
    }

    /**
     * Every in-process request builds a fresh container with its own DB connection that is never released
     * (the runner's error handler keeps it alive), so a long suite would exhaust PostgreSQL's connection limit.
     */
    private function dropLeakedAppConnections(): void
    {
        $this->pdo()->exec(
            'SELECT pg_terminate_backend(pid) FROM pg_stat_activity
             WHERE datname = current_database() AND pid <> pg_backend_pid()',
        );
    }

    private ?PDO $pdo = null;

    private function pdo(): PDO
    {
        return $this->pdo ??= new PDO(
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
