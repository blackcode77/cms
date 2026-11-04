<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;
use Psr\Http\Message\ResponseInterface;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertSame;

final class OrderRequestCest
{
    private const string REQUEST = '/api/v1/orders/request';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function createsClientAndOrderWithStatusRequest(ApiTester $I): void
    {
        $response = $this->submit($I, [
            'phone' => '8 (999) 123-45-67',
            'name' => 'Иван',
            'email' => 'Ivan@Example.com',
            'from' => 'Шанхай',
            'to' => 'Москва',
            'cargo_type' => "Контейнер 40'",
            'weight' => '20',
            'message' => 'Нужен расчёт',
        ]);

        assertSame(201, $response->getStatusCode());
        assertSame('success', $I->decode($response)['status']);
        $client = $I->fetchRow('SELECT * FROM clients');
        assertSame('+79991234567', $client['phone']);
        assertSame('Иван', $client['first_name']);
        assertSame('ivan@example.com', $client['email']);
        $order = $I->fetchRow('SELECT * FROM orders');
        assertSame($client['id'], $order['client_id']);
        assertSame('request', $order['status']);
        assertGreaterThanOrEqual(1000, (int) $order['number']);
        assertSame('Шанхай', $order['cargo_from']);
        assertSame('Москва', $order['cargo_to']);
        assertSame("Контейнер 40'", $order['cargo_type']);
        assertSame('20', $order['cargo_weight']);
        assertSame('Нужен расчёт', $order['message']);
        assertSame('', $order['comment']);
        assertSame(null, $order['manager_id']);
    }

    public function onlyPhoneIsRequired(ApiTester $I): void
    {
        assertSame(201, $this->submit($I, ['phone' => '+86 138 0013 8000'])->getStatusCode());
        assertSame('+8613800138000', $I->fetchRow('SELECT phone FROM clients')['phone']);

        $missing = $this->submit($I, ['name' => 'Иван'], '10.0.0.2');
        assertSame(422, $missing->getStatusCode());
        assertArrayHasKey('phone', $I->decode($missing)['error_data']);

        $invalid = $this->submit($I, ['phone' => 'позвоните мне'], '10.0.0.3');
        assertSame(422, $invalid->getStatusCode());
        assertArrayHasKey('phone', $I->decode($invalid)['error_data']);

        $badEmail = $this->submit($I, ['phone' => '+79990000000', 'email' => 'nope'], '10.0.0.4');
        assertSame(422, $badEmail->getStatusCode());
        assertArrayHasKey('email', $I->decode($badEmail)['error_data']);

        assertSame(1, $I->countRows('orders'));
    }

    public function samePhoneGoesToTheSameClientAndFillsOnlyMissingDetails(ApiTester $I): void
    {
        $this->submit($I, ['phone' => '+7 999 123-45-67', 'name' => 'Иван'], '10.0.0.1');
        $this->submit($I, ['phone' => '89991234567', 'name' => 'Пётр', 'email' => 'p@example.com'], '10.0.0.2');

        assertSame(1, $I->countRows('clients'));
        assertSame(2, $I->countRows('orders'));
        $client = $I->fetchRow('SELECT * FROM clients');
        assertSame('Иван', $client['first_name']);
        assertSame('p@example.com', $client['email']);
        $numbers = array_map('intval', $I->fetchColumn('SELECT number FROM orders ORDER BY number'));
        assertSame($numbers[0] + 1, $numbers[1]);
    }

    public function fourthRequestFromOneAddressWithinADayIsRejected(ApiTester $I): void
    {
        foreach (['+79990000001', '+79990000002', '+79990000003'] as $phone) {
            assertSame(201, $this->submit($I, ['phone' => $phone])->getStatusCode());
        }

        $fourth = $this->submit($I, ['phone' => '+79990000004']);

        assertSame(400, $fourth->getStatusCode());
        assertSame(
            'Вы уже отправили несколько запросов, мы скоро с вами свяжемся.',
            $I->decode($fourth)['error_message'],
        );
        assertSame(3, $I->countRows('orders'));

        $I->travelTo('+25 hours');
        assertSame(201, $this->submit($I, ['phone' => '+79990000004'])->getStatusCode());
    }

    public function fourthRequestForOnePhoneWithinADayIsRejected(ApiTester $I): void
    {
        foreach (['10.0.0.1', '10.0.0.2', '10.0.0.3'] as $ip) {
            assertSame(201, $this->submit($I, ['phone' => '+79990000001'], $ip)->getStatusCode());
        }

        assertSame(400, $this->submit($I, ['phone' => '8 999 000-00-01'], '10.0.0.4')->getStatusCode());
        assertSame(3, $I->countRows('orders'));
    }

    public function moreThanTenRequestsPerMinuteAreRejectedWith429(ApiTester $I): void
    {
        $I->travelTo('2026-11-05 12:00:05');
        for ($i = 1; $i <= 10; $i++) {
            $status = $this->submit($I, ['phone' => sprintf('+7999000%04d', $i)])->getStatusCode();
            assertSame($i <= 3 ? 201 : 400, $status);
        }

        $eleventh = $this->submit($I, ['phone' => '+79990000011']);

        assertSame(429, $eleventh->getStatusCode());
        assertSame('failed', $I->decode($eleventh)['status']);
        assertSame('Слишком много запросов, попробуйте позже.', $I->decode($eleventh)['error_message']);
        // another address is not affected
        assertSame(201, $this->submit($I, ['phone' => '+79990000012'], '10.0.0.99')->getStatusCode());

        $I->travelTo('2026-11-05 12:01:00');
        assertSame(400, $this->submit($I, ['phone' => '+79990000013'])->getStatusCode());
    }

    public function ordersCreatedInTheAdminAreaDoNotCountTowardsTheQuota(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        for ($i = 0; $i < 3; $i++) {
            $I->sendApiRequest('POST', '/api/v1/orders', ['phone' => '+79990000001'], $admin, headers: ['X-Real-IP' => '10.0.0.1']);
        }

        assertSame(201, $this->submit($I, ['phone' => '+79990000001'])->getStatusCode());
    }

    /**
     * @param array<string, string> $body
     */
    private function submit(ApiTester $I, array $body, string $ip = '10.0.0.1'): ResponseInterface
    {
        return $I->sendApiRequest('POST', self::REQUEST, $body, headers: ['X-Real-IP' => $ip]);
    }
}
