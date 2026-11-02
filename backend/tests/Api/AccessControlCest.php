<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;
use PDO;

use function PHPUnit\Framework\assertSame;

final class AccessControlCest
{
    private const string CONTENT = '/api/v1/page-content/home';
    private const array PAYLOAD = ['path' => 'hero.title', 'type' => 'string', 'value' => 'One'];
    private const array SECOND = ['path' => 'hero.title', 'type' => 'string', 'value' => 'Two'];

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function publicEndpointsNeedNoSession(ApiTester $I): void
    {
        assertSame(200, $I->sendApiRequest('GET', '/api/v1/health')->getStatusCode());
        assertSame(200, $I->sendApiRequest('GET', '/api/v1/page-content?keys=home')->getStatusCode());
    }

    public function editingContentWithoutSessionReturns401(ApiTester $I): void
    {
        $response = $I->sendApiRequest('PATCH', self::CONTENT, self::PAYLOAD);

        assertSame(401, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(0, $I->countRows('page_content'));
    }

    public function editingContentWithGarbageTokenReturns401(ApiTester $I): void
    {
        $response = $I->sendApiRequest('PATCH', self::CONTENT, self::PAYLOAD, ['access_token' => 'garbage']);

        assertSame(401, $response->getStatusCode());
    }

    public function userWithoutEditPageGets403(ApiTester $I): void
    {
        $session = $I->haveSessionFor('viewer');

        $response = $I->sendApiRequest('PATCH', self::CONTENT, self::PAYLOAD, $session);

        assertSame(403, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(0, $I->countRows('page_content'));
    }

    public function adminAndManagerCanEditContent(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $manager = $I->haveSessionFor('manager');

        assertSame(200, $I->sendApiRequest('PATCH', self::CONTENT, self::PAYLOAD, $admin)->getStatusCode());
        assertSame(200, $I->sendApiRequest('PATCH', self::CONTENT, self::SECOND, $manager)->getStatusCode());
    }

    public function rolePermissionChangeAppliesOnNextRequest(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        assertSame(200, $I->sendApiRequest('PATCH', self::CONTENT, self::PAYLOAD, $session)->getStatusCode());

        $this->pdo()->exec("DELETE FROM role_permissions WHERE permission = 'edit_page'");
        assertSame(403, $I->sendApiRequest('PATCH', self::CONTENT, self::SECOND, $session)->getStatusCode());

        $this->pdo()->exec(
            "INSERT INTO role_permissions (role_id, permission) SELECT id, 'edit_page' FROM roles WHERE name = 'manager'",
        );
        assertSame(200, $I->sendApiRequest('PATCH', self::CONTENT, self::SECOND, $session)->getStatusCode());
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
