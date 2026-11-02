<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertSame;

final class PageCest
{
    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function listIsEmptyInitially(ApiTester $I): void
    {
        $response = $I->sendApiRequest('GET', '/api/v1/pages');

        assertSame(200, $response->getStatusCode());
        assertSame(['status' => 'success', 'data' => []], $I->decode($response));
    }

    public function createReturns201AndPageAppearsInList(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', '/api/v1/pages', [
            'title' => 'About us',
            'slug' => 'about-us',
            'content' => 'Hello',
        ], $I->haveSessionFor('manager'));

        assertSame(201, $response->getStatusCode());
        $id = $I->decode($response)['data']['id'];
        assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}$/', $id);

        $list = $I->decode($I->sendApiRequest('GET', '/api/v1/pages'))['data'];
        assertCount(1, $list);
        assertSame($id, $list[0]['id']);
        assertSame('About us', $list[0]['title']);
        assertSame('about-us', $list[0]['slug']);
        assertArrayHasKey('created_at', $list[0]);
    }

    public function createRejectsInvalidInput(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', '/api/v1/pages', [
            'title' => '',
            'slug' => 'Not A Slug',
        ], $I->haveSessionFor('manager'));

        assertSame(422, $response->getStatusCode());
        $body = $I->decode($response);
        assertSame('failed', $body['status']);
        assertArrayHasKey('title', $body['error_data']);
        assertArrayHasKey('slug', $body['error_data']);
        assertSame(0, $I->countRows('page'));
    }

    public function createRejectsDuplicateSlug(ApiTester $I): void
    {
        $payload = ['title' => 'Page', 'slug' => 'same-slug', 'content' => ''];
        $session = $I->haveSessionFor('manager');
        $I->sendApiRequest('POST', '/api/v1/pages', $payload, $session);

        $response = $I->sendApiRequest('POST', '/api/v1/pages', $payload, $session);

        assertSame(400, $response->getStatusCode());
        assertSame('Page with slug "same-slug" already exists.', $I->decode($response)['error_message']);
        assertSame(1, $I->countRows('page'));
    }
}
