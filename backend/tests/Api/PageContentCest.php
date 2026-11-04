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

        assertSame(200, $response->getStatusCode(), (string) $response->getBody());
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

    public function iconIsStoredAndBadNameRejected(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');

        $ok = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'a', 'type' => 'icon', 'value' => 'map-pin'], $session);
        $bad = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', ['path' => 'b', 'type' => 'icon', 'value' => '<svg>'], $session);

        assertSame(200, $ok->getStatusCode());
        assertSame(400, $bad->getStatusCode());
        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];
        assertSame(['type' => 'icon', 'value' => 'map-pin'], $home['a']);
        assertSame(false, isset($home['b']));
    }

    public function listIsStoredInOrderAndHtmlFieldsSanitized(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $items = [
            ['id' => 'b2', 'icon' => 'truck', 'title' => '<b>Fleet</b>', 'text' => '<p>Own <b>ships</b></p><script>alert(1)</script>', 'image' => '/img/a.jpg'],
            ['id' => 'a1', 'icon' => 'globe', 'title' => 'Agents', 'text' => ''],
        ];

        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', [
            'path' => 'why.items',
            'type' => 'list',
            'fields' => ['icon' => 'icon', 'title' => 'string', 'text' => 'html', 'image' => 'image'],
            'value' => $items,
        ], $session);

        assertSame(200, $response->getStatusCode());
        $stored = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home']['why.items'];
        assertSame('list', $stored['type']);
        assertSame(['b2', 'a1'], array_column($stored['value'], 'id'));
        assertSame('<b>Fleet</b>', $stored['value'][0]['title']);
        assertStringContainsString('<b>ships</b>', $stored['value'][0]['text']);
        assertStringNotContainsString('<script', $stored['value'][0]['text']);
        assertSame('/img/a.jpg', $stored['value'][0]['image']);
    }

    public function emptyListIsStored(ApiTester $I): void
    {
        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', [
            'path' => 'about.checklist',
            'type' => 'list',
            'fields' => ['text' => 'string'],
            'value' => [],
        ], $I->haveSessionFor('manager'));

        assertSame(200, $response->getStatusCode());
        $stored = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home']['about.checklist'];
        assertSame(['type' => 'list', 'value' => []], $stored);
    }

    public function rejectsMalformedList(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $fields = ['title' => 'string'];
        $cases = [
            'no fields' => ['value' => [['id' => 'a', 'title' => 'x']]],
            'string value' => ['fields' => $fields, 'value' => 'x'],
            'fields for scalar type' => ['type' => 'string', 'fields' => $fields, 'value' => 'x'],
            'unknown field type' => ['fields' => ['title' => 'video'], 'value' => []],
            'nested list field' => ['fields' => ['title' => 'list'], 'value' => []],
            'field named id' => ['fields' => ['id' => 'string'], 'value' => []],
            'undeclared field' => ['fields' => $fields, 'value' => [['id' => 'a', 'other' => 'x']]],
            'missing id' => ['fields' => $fields, 'value' => [['title' => 'x']]],
            'duplicate id' => ['fields' => $fields, 'value' => [['id' => 'a'], ['id' => 'a']]],
            'non-string field' => ['fields' => $fields, 'value' => [['id' => 'a', 'title' => ['x']]]],
            'item is not an object' => ['fields' => $fields, 'value' => ['x']],
        ];

        foreach ($cases as $name => $payload) {
            $response = $I->sendApiRequest(
                'PATCH',
                '/api/v1/page-content/home',
                ['path' => 'l', 'type' => 'list'] + $payload,
                $session,
            );
            assertSame(422, $response->getStatusCode(), $name);
        }
        assertSame(0, $I->countRows('page_content'));
    }

    public function rejectsListBreakingFieldRules(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $fields = ['title' => 'string', 'image' => 'image', 'icon' => 'icon'];
        $cases = [
            'too many items' => array_map(static fn(int $i): array => ['id' => "i$i"], range(1, 101)),
            'oversized field' => [['id' => 'a', 'title' => str_repeat('x', 256)]],
            'bad image' => [['id' => 'a', 'image' => 'https://evil.example/x.jpg']],
            'bad icon' => [['id' => 'a', 'icon' => 'Bad Icon']],
        ];

        foreach ($cases as $name => $value) {
            $response = $I->sendApiRequest(
                'PATCH',
                '/api/v1/page-content/home',
                ['path' => 'l', 'type' => 'list', 'fields' => $fields, 'value' => $value],
                $session,
            );
            assertSame(400, $response->getStatusCode(), $name);
        }
        assertSame(0, $I->countRows('page_content'));
    }

    public function linkAndLinesAreStoredAndBadLinksRejected(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $send = static fn(string $path, string $type, string $value) => $I->sendApiRequest(
            'PATCH',
            '/api/v1/page-content/home',
            ['path' => $path, 'type' => $type, 'value' => $value],
            $session,
        );

        assertSame(200, $send('a', 'link', '/services#agent')->getStatusCode());
        assertSame(200, $send('b', 'link', '')->getStatusCode());
        assertSame(200, $send('c', 'lines', "one\ntwo")->getStatusCode());
        foreach (['javascript:alert(1)', 'https://evil.example', '//evil.example', 'services', '/a b', '/x#', str_repeat('/a', 200)] as $bad) {
            assertSame(400, $send('d', 'link', $bad)->getStatusCode(), $bad);
        }

        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];
        assertSame('/services#agent', $home['a']['value']);
        assertSame('', $home['b']['value']);
        assertSame("one\ntwo", $home['c']['value']);
        assertSame(false, isset($home['d']));
    }

    public function urlAcceptsOnlyHttpsAddresses(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $send = static fn(string $value) => $I->sendApiRequest(
            'PATCH',
            '/api/v1/page-content/about',
            ['path' => 'company.url', 'type' => 'url', 'value' => $value],
            $session,
        );

        foreach (['', 'https://garant-intermodal.com', 'https://sub.example.ru/path?q=1#x'] as $ok) {
            assertSame(200, $send($ok)->getStatusCode(), $ok);
        }
        foreach (['http://example.com', 'javascript:alert(1)', 'https://', 'https://localhost', '//example.com', '/about', 'https://ex ample.com', 'https://example.com/"onmouseover="x'] as $bad) {
            assertSame(400, $send($bad)->getStatusCode(), $bad);
        }
    }

    public function listLinkFieldsAreValidated(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $payload = static fn(string $link): array => [
            'path' => 'services.cards',
            'type' => 'list',
            'fields' => ['title' => 'string', 'link' => 'link', 'points' => 'lines'],
            'value' => [['id' => 'a', 'title' => 'A', 'link' => $link, 'points' => "x\ny"]],
        ];

        assertSame(200, $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $payload('/fleet'), $session)->getStatusCode());
        assertSame(400, $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $payload('javascript:x'), $session)->getStatusCode());
    }

    public function routeListStoresColorAndStopsCanonically(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $payload = static fn(string $color, string $stops): array => [
            'path' => 'routes.items',
            'type' => 'list',
            'fields' => ['name' => 'string', 'color' => 'color', 'stops' => 'stops'],
            'value' => [['id' => 'r1', 'name' => 'Китай → Россия', 'color' => $color, 'stops' => $stops]],
        ];
        $stops = json_encode([
            ['x' => 10.04, 'y' => 80, 'label' => 'Шанхай', 'side' => 'left', 'note' => 'x'],
            ['x' => 70, 'y' => 20.5, 'label' => "Владивосток\nВМРП / ВМТП", 'side' => 'right'],
        ], JSON_THROW_ON_ERROR);

        $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $payload('#E4572E', $stops), $session);

        assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        $item = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home']['routes.items']['value'][0];
        assertSame('#e4572e', $item['color']);
        assertSame(
            '[{"x":10.0,"y":80.0,"label":"Шанхай","side":"left"},{"x":70.0,"y":20.5,"label":"Владивосток\nВМРП / ВМТП","side":"right"}]',
            $item['stops'],
        );

        $oneStop = json_encode([['x' => 1, 'y' => 1, 'label' => 'A', 'side' => 'top']], JSON_THROW_ON_ERROR);
        foreach (['bad color' => $payload('red', $stops), 'one stop' => $payload('#000000', $oneStop), 'broken json' => $payload('#000000', '[')] as $name => $bad) {
            assertSame(400, $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $bad, $session)->getStatusCode(), $name);
        }
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
