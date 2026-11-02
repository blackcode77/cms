<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

final class PageContentCest
{
    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function unknownPageReadsAsEmptyObject(ApiTester $I): void
    {
        $response = $I->sendApiRequest('GET', '/api/v1/page-content?keys=home,site');

        assertSame(200, $response->getStatusCode());
        assertSame('{"status":"success","data":{"home":{},"site":{}}}', (string) $response->getBody());
    }

    public function readRequiresValidKeys(ApiTester $I): void
    {
        assertSame(400, $I->sendApiRequest('GET', '/api/v1/page-content')->getStatusCode());
        assertSame(400, $I->sendApiRequest('GET', '/api/v1/page-content?keys=Bad Key')->getStatusCode());
    }

    public function savedValueIsReturnedByNextRead(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');

        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', [
            'path' => 'header.phone_number_one',
            'type' => 'string',
            'value' => '+7 (423) 000-00-00',
        ], $session);

        assertSame(200, $response->getStatusCode());
        $data = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home,contacts'))['data'];
        assertSame(
            ['type' => 'string', 'value' => '+7 (423) 000-00-00'],
            $data['home']['header.phone_number_one'],
        );
        assertSame([], $data['contacts']);
    }

    public function editsOfDifferentPathsOfOnePageBothSurvive(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'type' => 'string', 'value' => 'A'], $session);
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'b', 'type' => 'text', 'value' => 'B'], $session);
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'type' => 'string', 'value' => 'A2'], $session);

        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];

        assertSame('A2', $home['a']['value']);
        assertSame('B', $home['b']['value']);
        assertSame(1, $I->countRows('page_content'));
    }

    public function enabledFlagIsStoredAndKeepsValue(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'type' => 'string', 'value' => 'A'], $session);

        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'enabled' => false], $session);
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'only_flag', 'enabled' => false], $session);

        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];
        assertSame(['type' => 'string', 'value' => 'A', 'enabled' => false], $home['a']);
        assertSame(['enabled' => false], $home['only_flag']);
    }

    public function htmlIsSanitizedBeforeStoring(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');

        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', [
            'path' => 'hero.title',
            'type' => 'html',
            'value' => '<p onclick="x()">Hi <b>there</b></p><script>alert(1)</script><a href="javascript:alert(1)">l</a><img src=x onerror=alert(1)>',
        ], $session);

        assertSame(200, $response->getStatusCode());
        $stored = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home']['hero.title']['value'];
        assertStringContainsString('<b>there</b>', $stored);
        foreach (['<script', 'alert(1)', 'onclick', 'onerror', 'javascript:'] as $bad) {
            assertStringNotContainsString($bad, $stored);
        }
    }

    public function plainTypesAreNotModified(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'type' => 'text', 'value' => "<b>1 & 2</b>\nline"], $session);

        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];

        assertSame("<b>1 & 2</b>\nline", $home['a']['value']);
    }

    public function rejectsInvalidInput(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $cases = [
            'bad path' => ['path' => 'Bad Path!', 'type' => 'string', 'value' => 'x'],
            'empty path' => ['path' => '', 'type' => 'string', 'value' => 'x'],
            'unknown type' => ['path' => 'a', 'type' => 'video', 'value' => 'x'],
            'value without type' => ['path' => 'a', 'value' => 'x'],
            'nothing to change' => ['path' => 'a'],
        ];

        foreach ($cases as $name => $payload) {
            $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $payload, $session);
            assertSame(422, $response->getStatusCode(), $name);
        }
        assertSame(0, $I->countRows('page_content'));
    }

    public function rejectsOversizedValue(ApiTester $I): void
    {
        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', [
            'path' => 'a',
            'type' => 'string',
            'value' => str_repeat('x', 256),
        ], $I->haveSessionFor('manager'));

        assertSame(400, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(0, $I->countRows('page_content'));
    }

    public function invalidPageKeyIsNotFound(ApiTester $I): void
    {
        $response = $I->sendApiRequest(
            'PATCH',
            '/api/v1/page-content/Bad_Key',
            ['path' => 'a', 'type' => 'string', 'value' => 'x'],
            $I->haveSessionFor('manager'),
        );

        assertSame(404, $response->getStatusCode());
    }
}
