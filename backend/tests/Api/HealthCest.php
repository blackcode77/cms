<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertSame;

final class HealthCest
{
    public function healthReportsDatabaseOk(ApiTester $I): void
    {
        $response = $I->sendApiRequest('GET', '/api/v1/health');

        assertSame(200, $response->getStatusCode());
        assertSame(
            ['status' => 'success', 'data' => ['app' => 'ok', 'database' => 'ok']],
            $I->decode($response),
        );
    }

    public function unknownRouteReturnsJsonNotFound(ApiTester $I): void
    {
        $response = $I->sendApiRequest('GET', '/api/v1/nope');

        assertSame(404, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
    }
}
