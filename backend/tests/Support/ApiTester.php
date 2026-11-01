<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Environment;
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

    public function sendApiRequest(string $method, string $uri, ?array $json = null): ResponseInterface
    {
        $request = new ServerRequest(method: $method, uri: $uri);
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
        $this->pdo()->exec('TRUNCATE TABLE page');
    }

    public function countRows(string $table): int
    {
        return (int) $this->pdo()->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();
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
