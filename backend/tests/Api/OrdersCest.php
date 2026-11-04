<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

final class OrdersCest
{
    private const string ORDERS = '/api/v1/orders';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function endpointsRequireOrderPermissions(ApiTester $I): void
    {
        assertSame(401, $I->sendApiRequest('GET', self::ORDERS)->getStatusCode());
        $I->haveRoleWithPermissions('viewer', ['view_orders']);
        $viewer = $I->haveSessionFor('viewer');
        $nobody = $I->haveSessionFor('visiter');

        assertSame(403, $I->sendApiRequest('GET', self::ORDERS, null, $nobody)->getStatusCode());
        assertSame(200, $I->sendApiRequest('GET', self::ORDERS, null, $viewer)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', self::ORDERS, ['phone' => '+79990000001'], $viewer)->getStatusCode());
        assertSame(0, $I->countRows('orders'));
    }

    public function createListsAndAttachesToExistingClientByPhone(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', '/api/v1/clients', ['phone' => '+79991234567', 'last_name' => 'Иванов'], $admin);

        $response = $I->sendApiRequest('POST', self::ORDERS, [
            'phone' => '8 999 123 45 67',
            'first_name' => 'Иван',
            'last_name' => 'Петров',
            'status' => 'in_progress',
            'from' => 'Шанхай',
            'comment' => 'Перезвонить завтра',
        ], $admin);

        assertSame(201, $response->getStatusCode());
        $created = $I->decode($response)['data'];
        assertSame(1, $I->countRows('clients'));
        $client = $I->fetchRow('SELECT * FROM clients');
        assertSame($client['id'], $created['client_id']);
        assertSame('Иванов', $client['last_name']);
        assertSame('Иван', $client['first_name']);

        $list = $I->decode($I->sendApiRequest('GET', self::ORDERS, null, $admin))['data'];
        assertCount(1, $list);
        assertSame($created['number'], $list[0]['number']);
        assertSame('in_progress', $list[0]['status']);
        assertSame('+79991234567', $list[0]['client']['phone']);
        assertSame('Иванов', $list[0]['client']['last_name']);
        assertSame('Шанхай', $list[0]['from']);
        assertSame('Перезвонить завтра', $list[0]['comment']);
        assertSame(null, $list[0]['manager']);
    }

    public function listIsNewestFirstAndFilters(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $managerId = $I->userId('admin@example.com');
        $I->travelTo('+1 minute');
        $I->sendApiRequest('POST', self::ORDERS, ['phone' => '+79990000001'], $admin);
        $I->travelTo('+2 minutes');
        $I->sendApiRequest('POST', self::ORDERS, ['phone' => '+79990000002', 'status' => 'done', 'manager_id' => $managerId], $admin);

        $all = $I->decode($I->sendApiRequest('GET', self::ORDERS, null, $admin))['data'];
        assertSame(['+79990000002', '+79990000001'], array_column(array_column($all, 'client'), 'phone'));

        $done = $I->decode($I->sendApiRequest('GET', self::ORDERS . '?status=done', null, $admin))['data'];
        assertSame(['+79990000002'], array_column(array_column($done, 'client'), 'phone'));

        $mine = $I->decode($I->sendApiRequest('GET', self::ORDERS . '?manager=me', null, $admin))['data'];
        assertCount(1, $mine);
        assertSame(['id' => $managerId, 'name' => 'Test User'], $mine[0]['manager']);
    }

    public function updateChangesStatusManagerAndComment(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveUser('m@example.com', 'secret-pass', 'manager');
        $id = $this->createOrder($I, $admin);

        $response = $I->sendApiRequest('PUT', self::ORDERS . '/' . $id, [
            'status' => 'done',
            'manager_id' => $I->userId('m@example.com'),
            'comment' => 'Отправили КП',
            'message' => 'Исходный текст',
        ], $admin);

        assertSame(200, $response->getStatusCode());
        $order = $I->decode($I->sendApiRequest('GET', self::ORDERS . '/' . $id, null, $admin))['data'];
        assertSame('done', $order['status']);
        assertSame($I->userId('m@example.com'), $order['manager']['id']);
        assertSame('Отправили КП', $order['comment']);

        // and back: any status may follow any other
        $I->sendApiRequest('PUT', self::ORDERS . '/' . $id, ['status' => 'request'], $admin);
        assertSame('request', $I->decode($I->sendApiRequest('GET', self::ORDERS . '/' . $id, null, $admin))['data']['status']);
    }

    public function onlyUsersWithHandleOrdersCanBeManagers(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveRoleWithPermissions('writer', ['edit_page']);
        $I->haveUser('w@example.com', 'secret-pass', 'writer');
        $I->haveUser('m@example.com', 'secret-pass', 'manager');
        $id = $this->createOrder($I, $admin);

        $response = $I->sendApiRequest('PUT', self::ORDERS . '/' . $id, ['manager_id' => $I->userId('w@example.com')], $admin);

        assertSame(400, $response->getStatusCode());
        assertSame('Этого пользователя нельзя назначить менеджером заказа.', $I->decode($response)['error_message']);

        $managers = $I->decode($I->sendApiRequest('GET', self::ORDERS . '/managers', null, $admin))['data'];
        $emails = array_column($managers, 'email');
        sort($emails);
        assertSame(['admin@example.com', 'm@example.com'], $emails);
    }

    public function deletedManagerLeavesOrderUnassigned(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveUser('m@example.com', 'secret-pass', 'manager');
        $managerId = $I->userId('m@example.com');
        $id = $this->createOrder($I, $admin, ['manager_id' => $managerId]);

        $I->sendApiRequest('DELETE', '/api/v1/users/' . $managerId, null, $admin);

        assertSame(null, $I->decode($I->sendApiRequest('GET', self::ORDERS . '/' . $id, null, $admin))['data']['manager']);
    }

    public function deleteKeepsTheClient(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $id = $this->createOrder($I, $admin);

        assertSame(200, $I->sendApiRequest('DELETE', self::ORDERS . '/' . $id, null, $admin)->getStatusCode());

        assertSame(0, $I->countRows('orders'));
        assertSame(1, $I->countRows('clients'));
        assertSame(404, $I->sendApiRequest('DELETE', self::ORDERS . '/' . $id, null, $admin)->getStatusCode());
        assertSame(404, $I->sendApiRequest('GET', self::ORDERS . '/' . $id, null, $admin)->getStatusCode());
    }

    public function rejectsUnknownStatus(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest('POST', self::ORDERS, ['phone' => '+79990000001', 'status' => 'lost'], $admin);

        assertSame(422, $response->getStatusCode());
        assertSame(0, $I->countRows('orders'));
    }

    /**
     * @param array<string, string> $extra
     * @param array<string, string> $session
     */
    private function createOrder(ApiTester $I, array $session, array $extra = []): string
    {
        $response = $I->sendApiRequest('POST', self::ORDERS, ['phone' => '+79990000001', ...$extra], $session);

        return (string) $I->decode($response)['data']['id'];
    }
}
