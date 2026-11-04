<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

final class ClientsCest
{
    private const string CLIENTS = '/api/v1/clients';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function endpointsRequireClientPermissions(ApiTester $I): void
    {
        assertSame(401, $I->sendApiRequest('GET', self::CLIENTS)->getStatusCode());
        $I->haveRoleWithPermissions('viewer', ['view_clients']);
        $viewer = $I->haveSessionFor('viewer');

        assertSame(200, $I->sendApiRequest('GET', self::CLIENTS, null, $viewer)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+79990000001'], $viewer)->getStatusCode());
        assertSame(403, $I->sendApiRequest('GET', self::CLIENTS, null, $I->haveSessionFor('visiter'))->getStatusCode());
    }

    public function createListAndView(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest('POST', self::CLIENTS, [
            'phone' => '+7 (999) 123-45-67',
            'first_name' => 'Иван',
            'last_name' => 'Иванов',
            'middle_name' => 'Иванович',
            'email' => 'ivan@example.com',
            'comment' => 'Постоянный клиент',
        ], $admin);

        assertSame(201, $response->getStatusCode());
        $id = $I->decode($response)['data']['id'];
        $I->sendApiRequest('POST', '/api/v1/orders', ['phone' => '+79991234567'], $admin);

        $list = $I->decode($I->sendApiRequest('GET', self::CLIENTS, null, $admin))['data'];
        assertCount(1, $list);
        assertSame('+79991234567', $list[0]['phone']);
        assertSame('Иванович', $list[0]['middle_name']);
        assertSame('Постоянный клиент', $list[0]['comment']);
        assertSame(1, $list[0]['orders_count']);

        $view = $I->decode($I->sendApiRequest('GET', self::CLIENTS . '/' . $id, null, $admin))['data'];
        assertSame('ivan@example.com', $view['email']);
        assertCount(1, $view['orders']);
    }

    public function searchMatchesPhoneDigitsNameAndEmail(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+79991234567', 'last_name' => 'Иванов'], $admin);
        $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+8613800138000', 'email' => 'li@example.cn'], $admin);

        $find = fn(string $q): array => array_column(
            $I->decode($I->sendApiRequest('GET', self::CLIENTS . '?q=' . urlencode($q), null, $admin))['data'],
            'phone',
        );

        assertSame(['+79991234567'], $find('123-45'));
        assertSame(['+79991234567'], $find('иванов'));
        assertSame(['+8613800138000'], $find('LI@'));
        assertCount(2, $find(''));
    }

    public function phoneMustStayUnique(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+79990000001'], $admin);
        $second = $I->decode($I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+79990000002'], $admin))['data']['id'];

        $duplicate = $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '89990000001'], $admin);
        assertSame(400, $duplicate->getStatusCode());
        assertSame('Клиент с таким телефоном уже существует.', $I->decode($duplicate)['error_message']);

        $update = $I->sendApiRequest('PUT', self::CLIENTS . '/' . $second, ['phone' => '+79990000001'], $admin);
        assertSame(400, $update->getStatusCode());

        $ok = $I->sendApiRequest('PUT', self::CLIENTS . '/' . $second, ['phone' => '+79990000003', 'first_name' => 'Анна'], $admin);
        assertSame(200, $ok->getStatusCode());
        assertSame('Анна', $I->decode($I->sendApiRequest('GET', self::CLIENTS . '/' . $second, null, $admin))['data']['first_name']);
    }

    public function byPhoneFindsTheClientAnOrderWouldGoTo(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', self::CLIENTS, ['phone' => '+79991234567', 'last_name' => 'Иванов'], $admin);

        $found = $I->decode($I->sendApiRequest('GET', self::CLIENTS . '/by-phone?phone=' . urlencode('8 999 123-45-67'), null, $admin));
        assertSame('Иванов', $found['data']['last_name']);

        $missing = $I->decode($I->sendApiRequest('GET', self::CLIENTS . '/by-phone?phone=%2B79990000000', null, $admin));
        assertSame(null, $missing['data']);
    }

    public function deleteRemovesTheClientWithOrders(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', '/api/v1/orders', ['phone' => '+79990000001'], $admin);
        $I->sendApiRequest('POST', '/api/v1/orders', ['phone' => '+79990000001'], $admin);
        $I->sendApiRequest('POST', '/api/v1/orders', ['phone' => '+79990000002'], $admin);
        $id = (string) $I->fetchRow("SELECT id FROM clients WHERE phone = '+79990000001'")['id'];

        assertSame(200, $I->sendApiRequest('DELETE', self::CLIENTS . '/' . $id, null, $admin)->getStatusCode());

        assertSame(1, $I->countRows('clients'));
        assertSame(1, $I->countRows('orders'));
        assertSame(404, $I->sendApiRequest('GET', self::CLIENTS . '/' . $id, null, $admin)->getStatusCode());
    }
}
