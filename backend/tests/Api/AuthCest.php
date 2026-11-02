<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;
use Psr\Http\Message\ResponseInterface;

use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class AuthCest
{
    private const string LOGIN = '/api/v1/auth/login';
    private const string REFRESH = '/api/v1/auth/refresh';
    private const string LOGOUT = '/api/v1/auth/logout';
    private const string ME = '/api/v1/auth/me';
    private const string GENERIC_ERROR = 'Invalid email or password.';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
        $I->haveUser('manager@example.com', 'secret-pass', 'manager');
    }

    public function loginSetsCookiesAndMeReturnsUserRoleAndPermissions(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::LOGIN, [
            'email' => 'Manager@Example.com',
            'password' => 'secret-pass',
        ]);

        assertSame(200, $response->getStatusCode());
        $cookies = $I->setCookies($response);
        foreach (['access_token', 'refresh_token'] as $name) {
            assertTrue($cookies[$name]['httponly'] ?? false);
            assertSame('Lax', $cookies[$name]['samesite']);
            assertTrue($cookies[$name]['secure'] ?? false, 'Secure outside dev');
        }
        assertSame('900', $cookies['access_token']['max-age']);
        assertSame('604800', $cookies['refresh_token']['max-age']);

        $me = $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => $cookies['access_token']['value']]);

        assertSame(200, $me->getStatusCode());
        $data = $I->decode($me)['data'];
        assertSame('manager@example.com', $data['user']['email']);
        assertSame('Test User', $data['user']['name']);
        assertSame('manager', $data['role']['name']);
        assertSame(['edit_page', 'view_dashboard'], $this->sorted($data['permissions']));
        assertSame(false, $data['must_change_password']);
        assertArrayNotHasKey('password_hash', $data['user']);
    }

    public function accessTokenContainsOnlyUserId(ApiTester $I): void
    {
        $cookies = $I->cookieValues($this->login($I));

        $payload = json_decode(
            base64_decode(strtr(explode('.', $cookies['access_token'])[1], '-_', '+/')),
            true,
        );
        assertSame(['exp', 'iat', 'sub'], $this->sorted(array_keys($payload)));
        assertSame(900, $payload['exp'] - $payload['iat']);
    }

    public function wrongPasswordAndUnknownEmailGiveTheSameError(ApiTester $I): void
    {
        $wrong = $this->login($I, 'manager@example.com', 'nope');
        $unknown = $this->login($I, 'ghost@example.com', 'nope');

        assertSame(400, $wrong->getStatusCode());
        assertSame($I->decode($wrong), $I->decode($unknown));
        assertSame(self::GENERIC_ERROR, $I->decode($wrong)['error_message']);
        assertSame([], $wrong->getHeader('Set-Cookie'));
    }

    public function fiveFailuresLockEmailAndIpForFifteenMinutes(ApiTester $I): void
    {
        $ip = ['X-Real-IP' => '10.0.0.1'];
        for ($i = 0; $i < 5; $i++) {
            $r = $this->login($I, 'manager@example.com', 'bad', $ip);
            assertSame(self::GENERIC_ERROR, $I->decode($r)['error_message']);
        }

        $locked = $this->login($I, headers: $ip);
        assertSame(400, $locked->getStatusCode());
        assertNotSame(self::GENERIC_ERROR, $I->decode($locked)['error_message']);
        assertSame([], $locked->getHeader('Set-Cookie'));

        // another IP is not affected
        assertSame(200, $this->login($I, headers: ['X-Real-IP' => '10.0.0.2'])->getStatusCode());

        $I->travelTo('+14 minutes');
        assertSame(400, $this->login($I, headers: $ip)->getStatusCode());

        $I->travelTo('+16 minutes');
        assertSame(200, $this->login($I, headers: $ip)->getStatusCode());
    }

    public function lockoutKeysOnTheIpFromTheProxyNotTheSocketAddress(ApiTester $I): void
    {
        // behind nginx REMOTE_ADDR is always the proxy: an attacker must not lock out everybody
        $proxy = ['REMOTE_ADDR' => '172.18.0.2'];
        $attacker = ['X-Real-IP' => '203.0.113.7'];
        $credentials = ['email' => 'manager@example.com', 'password' => 'bad'];
        for ($i = 0; $i < 5; $i++) {
            $I->sendApiRequest('POST', self::LOGIN, $credentials, serverParams: $proxy, headers: $attacker);
        }
        $credentials['password'] = 'secret-pass';

        $legit = $I->sendApiRequest('POST', self::LOGIN, $credentials, serverParams: $proxy, headers: ['X-Real-IP' => '198.51.100.9']);
        $blocked = $I->sendApiRequest('POST', self::LOGIN, $credentials, serverParams: $proxy, headers: $attacker);

        assertSame(200, $legit->getStatusCode());
        assertSame(400, $blocked->getStatusCode());
    }

    public function attemptsAreCountedBeforeThePasswordIsChecked(ApiTester $I): void
    {
        // at most five password checks per window: afterwards even the correct password is refused
        $ip = ['X-Real-IP' => '10.0.0.5'];
        for ($i = 0; $i < 5; $i++) {
            $this->login($I, 'manager@example.com', 'bad', $ip);
        }

        assertNotSame(self::GENERIC_ERROR, $I->decode($this->login($I, headers: $ip))['error_message']);
    }

    public function expiredFailureRowsArePurged(ApiTester $I): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->login($I, 'ghost' . $i . '@example.com', 'bad');
        }
        assertSame(3, $I->countRows('login_attempts'));

        $I->travelTo('+16 minutes');
        $this->login($I, 'ghost9@example.com', 'bad');

        assertSame(1, $I->countRows('login_attempts'));
    }

    public function successfulLoginRemovesTheCounterRow(ApiTester $I): void
    {
        $this->login($I, 'manager@example.com', 'bad');
        $this->login($I);

        assertSame(0, $I->countRows('login_attempts'));
    }

    public function successfulLoginResetsFailureCounter(ApiTester $I): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->login($I, 'manager@example.com', 'bad');
        }
        assertSame(200, $this->login($I)->getStatusCode());
        for ($i = 0; $i < 4; $i++) {
            $this->login($I, 'manager@example.com', 'bad');
        }

        assertSame(200, $this->login($I)->getStatusCode());
    }

    public function refreshIssuesNewPairAndOldRefreshTokenStopsWorking(ApiTester $I): void
    {
        $old = $I->cookieValues($this->login($I));

        $refreshed = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => $old['refresh_token']]);

        assertSame(200, $refreshed->getStatusCode());
        $new = $I->cookieValues($refreshed);
        assertNotSame($old['refresh_token'], $new['refresh_token']);
        $me = $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => $new['access_token']]);
        assertSame(200, $me->getStatusCode());

        $reuse = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => $old['refresh_token']]);
        assertSame(401, $reuse->getStatusCode());
        $again = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => $new['refresh_token']]);
        assertSame(200, $again->getStatusCode());
    }

    public function refreshWithoutOrWithGarbageTokenReturns401(ApiTester $I): void
    {
        assertSame(401, $I->sendApiRequest('POST', self::REFRESH)->getStatusCode());
        $garbage = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => 'garbage']);
        assertSame(401, $garbage->getStatusCode());
    }

    public function refreshTokenExpiresAfterSevenDays(ApiTester $I): void
    {
        $cookies = $I->cookieValues($this->login($I));

        $I->travelTo('+7 days +1 minute');

        $response = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => $cookies['refresh_token']]);
        assertSame(401, $response->getStatusCode());
    }

    public function refreshTokenIsStoredOnlyAsHash(ApiTester $I): void
    {
        $cookies = $I->cookieValues($this->login($I));

        assertSame(1, $I->countRows('refresh_tokens'));
        assertSame(0, $I->countRows("refresh_tokens WHERE token_hash = '" . $cookies['refresh_token'] . "'"));
    }

    public function logoutRevokesRefreshTokenAndClearsCookies(ApiTester $I): void
    {
        $cookies = $I->cookieValues($this->login($I));

        $logout = $I->sendApiRequest('POST', self::LOGOUT, cookies: ['refresh_token' => $cookies['refresh_token']]);

        assertSame(200, $logout->getStatusCode());
        $cleared = $I->setCookies($logout);
        assertSame('', $cleared['access_token']['value']);
        assertSame('', $cleared['refresh_token']['value']);
        $reuse = $I->sendApiRequest('POST', self::REFRESH, cookies: ['refresh_token' => $cookies['refresh_token']]);
        assertSame(401, $reuse->getStatusCode());
    }

    public function meWithoutValidAccessTokenReturns401(ApiTester $I): void
    {
        assertSame(401, $I->sendApiRequest('GET', self::ME)->getStatusCode());
        assertSame(401, $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => 'garbage'])->getStatusCode());
        $token = $I->cookieValues($this->login($I))['access_token'];
        $tampered = $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => $token . 'x']);
        assertSame(401, $tampered->getStatusCode());

        $I->travelTo('+16 minutes');

        $expired = $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => $token]);
        assertSame(401, $expired->getStatusCode());
        assertSame('failed', $I->decode($expired)['status']);
    }

    public function meReportsMustChangePassword(ApiTester $I): void
    {
        $I->haveUser('new@example.com', 'temp-pass', 'manager', mustChangePassword: true);
        $cookies = $I->cookieValues($this->login($I, 'new@example.com', 'temp-pass'));

        $me = $I->sendApiRequest('GET', self::ME, cookies: ['access_token' => $cookies['access_token']]);

        assertSame(true, $I->decode($me)['data']['must_change_password']);
    }

    /**
     * @param array<string, string> $headers
     */
    private function login(
        ApiTester $I,
        string $email = 'manager@example.com',
        string $password = 'secret-pass',
        array $headers = [],
    ): ResponseInterface {
        return $I->sendApiRequest(
            'POST',
            self::LOGIN,
            ['email' => $email, 'password' => $password],
            headers: $headers,
        );
    }

    /**
     * @param list<string> $values
     * @return list<string>
     */
    private function sorted(array $values): array
    {
        sort($values);

        return $values;
    }
}
