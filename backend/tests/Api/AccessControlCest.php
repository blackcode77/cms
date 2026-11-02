<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;
use PDO;

use function PHPUnit\Framework\assertSame;

final class AccessControlCest
{
    private const string PAGES = '/api/v1/pages';
    private const array PAYLOAD = ['title' => 'About', 'slug' => 'about', 'content' => ''];
    private const array SECOND = ['title' => 'Two', 'slug' => 'two', 'content' => ''];

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function publicEndpointsNeedNoSession(ApiTester $I): void
    {
        assertSame(200, $I->sendApiRequest('GET', '/api/v1/health')->getStatusCode());
        assertSame(200, $I->sendApiRequest('GET', self::PAGES)->getStatusCode());
    }

    public function creatingPageWithoutSessionReturns401(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::PAGES, self::PAYLOAD);

        assertSame(401, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(0, $I->countRows('page'));
    }

    public function creatingPageWithGarbageTokenReturns401(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::PAGES, self::PAYLOAD, ['access_token' => 'garbage']);

        assertSame(401, $response->getStatusCode());
    }

    public function userWithoutEditPageGets403(ApiTester $I): void
    {
        $session = $I->haveSessionFor('viewer');

        $response = $I->sendApiRequest('POST', self::PAGES, self::PAYLOAD, $session);

        assertSame(403, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(0, $I->countRows('page'));
    }

    public function adminAndManagerCanCreatePage(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $manager = $I->haveSessionFor('manager');

        assertSame(201, $I->sendApiRequest('POST', self::PAGES, self::PAYLOAD, $admin)->getStatusCode());
        assertSame(201, $I->sendApiRequest('POST', self::PAGES, self::SECOND, $manager)->getStatusCode());
    }

    public function rolePermissionChangeAppliesOnNextRequest(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        assertSame(201, $I->sendApiRequest('POST', self::PAGES, self::PAYLOAD, $session)->getStatusCode());

        $this->pdo()->exec("DELETE FROM role_permissions WHERE permission = 'edit_page'");
        assertSame(403, $I->sendApiRequest('POST', self::PAGES, self::SECOND, $session)->getStatusCode());

        $this->pdo()->exec(
            "INSERT INTO role_permissions (role_id, permission) SELECT id, 'edit_page' FROM roles WHERE name = 'manager'",
        );
        assertSame(201, $I->sendApiRequest('POST', self::PAGES, self::SECOND, $session)->getStatusCode());
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
