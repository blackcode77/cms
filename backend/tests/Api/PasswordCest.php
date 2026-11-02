<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

final class PasswordCest
{
    private const string CHANGE = '/api/v1/auth/change-password';
    private const string LOGIN = '/api/v1/auth/login';
    private const string ME = '/api/v1/auth/me';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function resetRequiresManageUsers(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass');
        $url = '/api/v1/users/' . $I->grabUserId('boss@example.com') . '/reset-password';

        assertSame(401, $I->sendApiRequest('POST', $url)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', $url, null, $I->haveSessionFor('manager'))->getStatusCode());
        assertCount(0, $I->sentMails());
    }

    public function resetEmailsNewPasswordAndOldOneStopsWorking(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('boss@example.com', 'old-password');
        $bossSession = $I->loginAs('boss@example.com', 'old-password');

        $response = $I->sendApiRequest(
            'POST',
            '/api/v1/users/' . $I->grabUserId('boss@example.com') . '/reset-password',
            null,
            $session,
        );

        assertSame(200, $response->getStatusCode());
        assertSame(true, $I->decode($response)['data']['mail_sent']);
        $mails = $I->sentMails();
        assertCount(1, $mails);
        assertSame('boss@example.com', $mails[0]['to']);
        assertSame(1, preg_match('/Password: (\S{16})\n/', $mails[0]['body'], $m));
        assertSame(400, $I->sendApiRequest('POST', self::LOGIN, ['email' => 'boss@example.com', 'password' => 'old-password'])->getStatusCode());
        assertSame(200, $I->sendApiRequest('POST', self::LOGIN, ['email' => 'boss@example.com', 'password' => $m[1]])->getStatusCode());
        // refresh tokens issued before are revoked
        assertSame(
            401,
            $I->sendApiRequest('POST', '/api/v1/auth/refresh', null, ['refresh_token' => $bossSession['refresh_token']])->getStatusCode(),
        );
    }

    public function resetFlagsUserToChangePassword(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('boss@example.com', 'old-password');

        $I->sendApiRequest('POST', '/api/v1/users/' . $I->grabUserId('boss@example.com') . '/reset-password', null, $session);
        preg_match('/Password: (\S{16})\n/', $I->sentMails()[0]['body'], $m);
        $me = $I->sendApiRequest('GET', self::ME, null, $I->loginAs('boss@example.com', $m[1]));

        assertTrue($I->decode($me)['data']['must_change_password']);
    }

    public function resetReportsMailFailure(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('boss@example.com', 'old-password');
        $oldHash = $I->grabPasswordHash('boss@example.com');
        $I->makeMailerFail();

        $response = $I->sendApiRequest('POST', '/api/v1/users/' . $I->grabUserId('boss@example.com') . '/reset-password', null, $session);

        assertSame(200, $response->getStatusCode());
        assertSame(false, $I->decode($response)['data']['mail_sent']);
        assertNotSame($oldHash, $I->grabPasswordHash('boss@example.com'));
    }

    public function resetUnknownUserIs404(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        assertSame(404, $I->sendApiRequest('POST', '/api/v1/users/0190a000-0000-7000-8000-000000000000/reset-password', null, $session)->getStatusCode());
        assertSame(404, $I->sendApiRequest('POST', '/api/v1/users/nope/reset-password', null, $session)->getStatusCode());
    }

    public function changePasswordRequiresSession(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'a', 'new_password' => 'long-enough-1']);

        assertSame(401, $response->getStatusCode());
    }

    public function changePasswordSwitchesPasswordAndClearsFlag(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass', 'manager', true);
        $session = $I->loginAs('boss@example.com', 'secret-pass');

        $response = $I->sendApiRequest(
            'POST',
            self::CHANGE,
            ['current_password' => 'secret-pass', 'new_password' => 'brand-new-pass'],
            $session,
        );

        assertSame(200, $response->getStatusCode());
        assertSame(400, $I->sendApiRequest('POST', self::LOGIN, ['email' => 'boss@example.com', 'password' => 'secret-pass'])->getStatusCode());
        $new = $I->loginAs('boss@example.com', 'brand-new-pass');
        $me = $I->sendApiRequest('GET', self::ME, null, $new);
        assertSame(false, $I->decode($me)['data']['must_change_password']);
    }

    public function changePasswordRejectsWrongCurrentPassword(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass', 'manager', true);
        $session = $I->loginAs('boss@example.com', 'secret-pass');
        $hash = $I->grabPasswordHash('boss@example.com');

        $response = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'wrong', 'new_password' => 'brand-new-pass'], $session);

        assertSame(400, $response->getStatusCode());
        assertStringContainsString('current password', strtolower($I->decode($response)['error_message']));
        assertSame($hash, $I->grabPasswordHash('boss@example.com'));
        $me = $I->sendApiRequest('GET', self::ME, null, $session);
        assertTrue($I->decode($me)['data']['must_change_password']);
    }

    public function changePasswordValidatesNewPassword(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass');
        $session = $I->loginAs('boss@example.com', 'secret-pass');

        foreach (['short', ''] as $bad) {
            $response = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'secret-pass', 'new_password' => $bad], $session);
            assertSame(422, $response->getStatusCode(), $bad);
        }
    }

    public function pendingPasswordChangeBlocksEveryOtherProtectedEndpoint(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass', 'admin', true);
        $session = $I->loginAs('boss@example.com', 'secret-pass');

        $users = $I->sendApiRequest('GET', '/api/v1/users', null, $session);
        $page = $I->sendApiRequest('POST', '/api/v1/pages', ['title' => 'A', 'slug' => 'a', 'content' => ''], $session);

        assertSame(403, $users->getStatusCode());
        assertStringContainsString('change your password', strtolower($I->decode($users)['error_message']));
        assertSame(403, $page->getStatusCode());
        assertSame(0, $I->countRows('page'));
        assertSame(200, $I->sendApiRequest('GET', self::ME, null, $session)->getStatusCode());
        assertSame(200, $I->sendApiRequest('POST', '/api/v1/auth/refresh', null, $session)->getStatusCode());
        assertSame(200, $I->sendApiRequest('POST', '/api/v1/auth/logout', null, $session)->getStatusCode());
    }

    public function changingThePasswordUnblocksTheSession(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass', 'admin', true);
        $session = $I->loginAs('boss@example.com', 'secret-pass');

        $change = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'secret-pass', 'new_password' => 'brand-new-pass'], $session);

        assertSame(200, $change->getStatusCode());
        assertSame(200, $I->sendApiRequest('GET', '/api/v1/users', null, $I->cookieValues($change))->getStatusCode());
    }

    public function changePasswordRevokesOtherSessionsAndKeepsTheCallerSignedIn(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass');
        $stolen = $I->loginAs('boss@example.com', 'secret-pass');
        $mine = $I->loginAs('boss@example.com', 'secret-pass');

        $change = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'secret-pass', 'new_password' => 'brand-new-pass'], $mine);

        assertSame(200, $change->getStatusCode());
        assertSame(401, $I->sendApiRequest('POST', '/api/v1/auth/refresh', null, ['refresh_token' => $stolen['refresh_token']])->getStatusCode());
        assertSame(401, $I->sendApiRequest('POST', '/api/v1/auth/refresh', null, ['refresh_token' => $mine['refresh_token']])->getStatusCode());
        $fresh = $I->cookieValues($change);
        assertSame(200, $I->sendApiRequest('GET', self::ME, null, $fresh)->getStatusCode());
        assertSame(200, $I->sendApiRequest('POST', '/api/v1/auth/refresh', null, $fresh)->getStatusCode());
    }

    public function changePasswordRejectsNewPasswordEqualToCurrent(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass');
        $session = $I->loginAs('boss@example.com', 'secret-pass');
        $hash = $I->grabPasswordHash('boss@example.com');

        $response = $I->sendApiRequest('POST', self::CHANGE, ['current_password' => 'secret-pass', 'new_password' => 'secret-pass'], $session);

        assertSame(400, $response->getStatusCode());
        assertSame($hash, $I->grabPasswordHash('boss@example.com'));
    }

    public function wrongCurrentPasswordIsThrottled(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass');
        $session = $I->loginAs('boss@example.com', 'secret-pass');
        $body = ['current_password' => 'wrong', 'new_password' => 'brand-new-pass'];
        for ($i = 0; $i < 5; $i++) {
            $I->sendApiRequest('POST', self::CHANGE, $body, $session);
        }

        $body['current_password'] = 'secret-pass';
        $response = $I->sendApiRequest('POST', self::CHANGE, $body, $session);

        assertSame(400, $response->getStatusCode());
        assertStringContainsString('too many', strtolower($I->decode($response)['error_message']));
        assertSame(200, $I->sendApiRequest('POST', self::LOGIN, ['email' => 'boss@example.com', 'password' => 'secret-pass'])->getStatusCode());
    }
}
