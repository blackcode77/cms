<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

final class UsersCest
{
    private const string USERS = '/api/v1/users';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function listRequiresSessionAndViewUsers(ApiTester $I): void
    {
        assertSame(401, $I->sendApiRequest('GET', self::USERS)->getStatusCode());
        assertSame(403, $I->sendApiRequest('GET', self::USERS, null, $I->haveSessionFor('manager'))->getStatusCode());
    }

    public function listShowsUsersWithTheirRole(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('boss@example.com', 'secret-pass', 'manager', true);

        $response = $I->sendApiRequest('GET', self::USERS, null, $session);

        assertSame(200, $response->getStatusCode());
        $data = $I->decode($response)['data'];
        assertCount(2, $data);
        $byEmail = array_column($data, null, 'email');
        assertSame('admin', $byEmail['admin@example.com']['role']['name']);
        assertSame('manager', $byEmail['boss@example.com']['role']['name']);
        assertSame(true, $byEmail['boss@example.com']['must_change_password']);
        assertSame('Test User', $byEmail['boss@example.com']['name']);
        assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $byEmail['boss@example.com']['id']);
        foreach ($data as $item) {
            assertArrayNotHasKey('password_hash', $item);
        }
    }

    public function assignableRolesExcludeVisiter(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveRole('visiter');
        $I->haveRole('manager');

        $response = $I->sendApiRequest('GET', self::USERS . '/assignable-roles', null, $session);

        assertSame(200, $response->getStatusCode());
        $names = array_column($I->decode($response)['data'], 'name');
        sort($names);
        assertSame(['admin', 'manager'], $names);
        assertSame(
            403,
            $I->sendApiRequest('GET', self::USERS . '/assignable-roles', null, $I->haveSessionFor('manager'))
                ->getStatusCode(),
        );
    }

    public function createRequiresManageUsers(ApiTester $I): void
    {
        $payload = ['email' => 'new@example.com', 'name' => 'New', 'role_id' => $I->haveRole('manager')];

        assertSame(401, $I->sendApiRequest('POST', self::USERS, $payload)->getStatusCode());
        assertSame(
            403,
            $I->sendApiRequest('POST', self::USERS, $payload, $I->haveSessionFor('manager'))->getStatusCode(),
        );
        assertSame(1, $I->countRows('users'));
        assertSame([], $I->sentMails());
    }

    public function createStoresUserAndEmailsLoginAndPassword(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'New.Person@Example.com', 'name' => 'New Person', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(201, $response->getStatusCode());
        $data = $I->decode($response)['data'];
        assertSame(true, $data['mail_sent']);
        assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $data['id']);

        $mails = $I->sentMails();
        assertCount(1, $mails);
        assertSame('new.person@example.com', $mails[0]['to']);
        assertStringContainsString('Login: new.person@example.com', $mails[0]['body']);
        assertSame(1, preg_match('/Password: (\S{16})(\s|$)/', $mails[0]['body'], $m));

        // The emailed credentials really sign the new User in, and force a password change.
        $login = $I->sendApiRequest(
            'POST',
            '/api/v1/auth/login',
            ['email' => 'new.person@example.com', 'password' => $m[1]],
        );
        assertSame(200, $login->getStatusCode());
        $me = $I->decode($I->sendApiRequest('GET', '/api/v1/auth/me', null, $I->cookieValues($login)))['data'];
        assertSame(true, $me['must_change_password']);
        assertSame('manager', $me['role']['name']);
        assertSame('New Person', $me['user']['name']);

        assertCount(2, $I->decode($I->sendApiRequest('GET', self::USERS, null, $session))['data']);
    }

    public function createStoresArgon2idHashNotThePlainPassword(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'h@example.com', 'name' => 'H', 'role_id' => $I->haveRole('admin')],
            $session,
        );

        $hash = $I->grabPasswordHash('h@example.com');
        assertStringContainsString('$argon2id$', $hash);
        assertSame(1, preg_match('/Password: (\S{16})/', $I->sentMails()[0]['body'], $m));
        assertStringNotContainsString($m[1], $hash);
    }

    public function createSucceedsEvenWhenMailFails(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->makeMailerFail();

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'x@example.com', 'name' => 'X', 'role_id' => $I->haveRole('admin')],
            $session,
        );

        assertSame(201, $response->getStatusCode());
        assertSame(false, $I->decode($response)['data']['mail_sent']);
        assertSame(2, $I->countRows('users'));
    }

    public function duplicateEmailIsBusinessError(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('dup@example.com', 'secret-pass', 'manager');

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'DUP@example.com', 'name' => 'Dup', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(400, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(2, $I->countRows('users'));
        assertSame([], $I->sentMails());
    }

    public function visiterCannotBeAssigned(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'g@example.com', 'name' => 'G', 'role_id' => $I->haveRole('visiter')],
            $session,
        );

        assertSame(400, $response->getStatusCode());
        assertSame(1, $I->countRows('users'));
        assertSame([], $I->sentMails());
    }

    public function unknownRoleIsBusinessError(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'g@example.com', 'name' => 'G', 'role_id' => '0199aaaa-0000-7000-8000-000000000000'],
            $session,
        );

        assertSame(400, $response->getStatusCode());
        assertSame(1, $I->countRows('users'));
    }

    public function createRejectsInvalidInput(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'POST',
            self::USERS,
            ['email' => 'not-an-email', 'name' => '', 'role_id' => 'nope'],
            $session,
        );

        assertSame(422, $response->getStatusCode());
        $errors = $I->decode($response)['error_data'];
        assertSame(true, isset($errors['email'], $errors['name'], $errors['role_id']));
    }

    public function updateAndDeleteRequireSessionAndManageUsers(ApiTester $I): void
    {
        $I->haveUser('t@example.com', 'secret-pass', 'manager');
        $id = $I->userId('t@example.com');
        $payload = ['email' => 't2@example.com', 'name' => 'T', 'role_id' => $I->haveRole('manager')];

        assertSame(401, $I->sendApiRequest('PUT', self::USERS . '/' . $id, $payload)->getStatusCode());
        assertSame(401, $I->sendApiRequest('DELETE', self::USERS . '/' . $id)->getStatusCode());
        $manager = $I->loginAs('t@example.com', 'secret-pass');
        assertSame(403, $I->sendApiRequest('PUT', self::USERS . '/' . $id, $payload, $manager)->getStatusCode());
        assertSame(403, $I->sendApiRequest('DELETE', self::USERS . '/' . $id, null, $manager)->getStatusCode());
        assertSame(1, $I->countRows('users'));
    }

    public function updateChangesNameEmailAndRole(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('t@example.com', 'secret-pass', 'manager');
        $id = $I->userId('t@example.com');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $id,
            ['email' => 'New@Example.com', 'name' => 'Renamed', 'role_id' => $I->haveRole('editor')],
            $session,
        );

        assertSame(200, $response->getStatusCode());
        $byId = array_column($I->decode($I->sendApiRequest('GET', self::USERS, null, $session))['data'], null, 'id');
        assertSame('new@example.com', $byId[$id]['email']);
        assertSame('Renamed', $byId[$id]['name']);
        assertSame('editor', $byId[$id]['role']['name']);
        // The password is untouched.
        assertSame(true, password_verify('secret-pass', $I->grabPasswordHash('new@example.com')));
    }

    public function updateKeepingOwnEmailIsAllowed(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('t@example.com', 'secret-pass', 'manager');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('t@example.com'),
            ['email' => 'T@example.com', 'name' => 'Same email', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(200, $response->getStatusCode());
    }

    public function updateRejectsDuplicateEmailCaseInsensitively(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('t@example.com', 'secret-pass', 'manager');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('t@example.com'),
            ['email' => 'ADMIN@example.com', 'name' => 'T', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(400, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
    }

    public function updateRejectsVisiterAndUnknownRole(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('t@example.com', 'secret-pass', 'manager');
        $url = self::USERS . '/' . $I->userId('t@example.com');

        $visiter = ['email' => 't@example.com', 'name' => 'T', 'role_id' => $I->haveRole('visiter')];
        assertSame(400, $I->sendApiRequest('PUT', $url, $visiter, $session)->getStatusCode());
        $unknown = ['email' => 't@example.com', 'name' => 'T', 'role_id' => '0199aaaa-0000-7000-8000-000000000000'];
        assertSame(400, $I->sendApiRequest('PUT', $url, $unknown, $session)->getStatusCode());
    }

    public function updateOfUnknownUserIsBusinessError(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/0199aaaa-0000-7000-8000-000000000000',
            ['email' => 't@example.com', 'name' => 'T', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(400, $response->getStatusCode());
    }

    public function updateRejectsInvalidInput(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('admin@example.com'),
            ['email' => 'nope', 'name' => '', 'role_id' => 'nope'],
            $session,
        );

        assertSame(422, $response->getStatusCode());
    }

    public function lastAdminCannotBeDemoted(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('admin@example.com'),
            ['email' => 'admin@example.com', 'name' => 'X', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(400, $response->getStatusCode());
        assertSame('admin', $I->grabRoleName('admin@example.com'));
    }

    public function adminCanBeDemotedWhenAnotherAdminExists(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('second@example.com', 'secret-pass', 'admin');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('second@example.com'),
            ['email' => 'second@example.com', 'name' => 'X', 'role_id' => $I->haveRole('manager')],
            $session,
        );

        assertSame(200, $response->getStatusCode());
        assertSame('manager', $I->grabRoleName('second@example.com'));
    }

    public function lastAdminCanStillBeRenamedKeepingTheRole(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'PUT',
            self::USERS . '/' . $I->userId('admin@example.com'),
            ['email' => 'admin@example.com', 'name' => 'Boss', 'role_id' => $I->haveRole('admin')],
            $session,
        );

        assertSame(200, $response->getStatusCode());
    }

    public function deleteRemovesUserAndTheirRefreshTokenStopsWorking(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('t@example.com', 'secret-pass', 'manager');
        $victim = $I->loginAs('t@example.com', 'secret-pass');
        $victimId = $I->userId('t@example.com');
        assertSame(1, $I->countRows('refresh_tokens', "user_id = '" . $victimId . "'"));

        $response = $I->sendApiRequest('DELETE', self::USERS . '/' . $victimId, null, $session);

        assertSame(200, $response->getStatusCode());
        assertSame(1, $I->countRows('users'));
        assertSame(0, $I->countRows('refresh_tokens', "user_id = '" . $victimId . "'"));
        assertSame(
            401,
            $I->sendApiRequest('POST', '/api/v1/auth/refresh', cookies: ['refresh_token' => $victim['refresh_token']])
                ->getStatusCode(),
        );
        assertSame(401, $I->sendApiRequest('GET', '/api/v1/auth/me', null, $victim)->getStatusCode());
    }

    public function cannotDeleteYourself(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('second@example.com', 'secret-pass', 'admin');

        $response = $I->sendApiRequest('DELETE', self::USERS . '/' . $I->userId('admin@example.com'), null, $session);

        assertSame(400, $response->getStatusCode());
        assertSame('failed', $I->decode($response)['status']);
        assertSame(2, $I->countRows('users'));
    }

    public function lastAdminCannotBeDeleted(ApiTester $I): void
    {
        $I->haveUser('boss@example.com', 'secret-pass', 'admin');
        $I->haveUser('mgr@example.com', 'secret-pass', 'manager');
        // A manager holding manage_users tries to delete the only Admin.
        $I->havePermission('manager', 'manage_users');
        $session = $I->loginAs('mgr@example.com', 'secret-pass');

        $response = $I->sendApiRequest('DELETE', self::USERS . '/' . $I->userId('boss@example.com'), null, $session);

        assertSame(400, $response->getStatusCode());
        assertSame(2, $I->countRows('users'));
    }

    public function anotherAdminCanBeDeletedWhenNotTheLast(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');
        $I->haveUser('second@example.com', 'secret-pass', 'admin');

        $response = $I->sendApiRequest('DELETE', self::USERS . '/' . $I->userId('second@example.com'), null, $session);

        assertSame(200, $response->getStatusCode());
        assertSame(1, $I->countRows('users'));
    }

    public function deleteOfUnknownUserIsBusinessError(ApiTester $I): void
    {
        $session = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'DELETE',
            self::USERS . '/0199aaaa-0000-7000-8000-000000000000',
            null,
            $session,
        );

        assertSame(400, $response->getStatusCode());
    }
}
