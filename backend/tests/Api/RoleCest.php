<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotContains;
use function PHPUnit\Framework\assertSame;

final class RoleCest
{
    private const string UNKNOWN_ID = '0198a1b2-0000-7000-8000-000000000000';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function endpointsRequireViewAndManagePermissions(ApiTester $I): void
    {
        $manager = $I->haveSessionFor('manager');
        $url = '/api/v1/roles/' . self::UNKNOWN_ID;

        assertSame(401, $I->sendApiRequest('GET', '/api/v1/roles')->getStatusCode());
        assertSame(403, $I->sendApiRequest('GET', '/api/v1/roles', null, $manager)->getStatusCode());
        assertSame(403, $I->sendApiRequest('GET', '/api/v1/permissions', null, $manager)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', '/api/v1/roles', ['name' => 'x'], $manager)->getStatusCode());
        assertSame(403, $I->sendApiRequest('PUT', $url, ['name' => 'x'], $manager)->getStatusCode());
        assertSame(403, $I->sendApiRequest('DELETE', $url, null, $manager)->getStatusCode());
    }

    public function viewRolesWithoutManageCanOnlyRead(ApiTester $I): void
    {
        $I->haveRole('viewer', ['view_roles']);
        $I->haveUser('viewer@example.com', 'secret-pass', 'viewer');
        $viewer = $I->loginAs('viewer@example.com', 'secret-pass');

        assertSame(200, $I->sendApiRequest('GET', '/api/v1/roles', null, $viewer)->getStatusCode());
        assertSame(200, $I->sendApiRequest('GET', '/api/v1/permissions', null, $viewer)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', '/api/v1/roles', ['name' => 'x'], $viewer)->getStatusCode());
    }

    public function permissionCatalogListsEveryPermission(ApiTester $I): void
    {
        $response = $I->sendApiRequest('GET', '/api/v1/permissions', null, $I->haveSessionFor('admin'));

        assertSame(200, $response->getStatusCode());
        assertSame(
            ['view_dashboard', 'edit_page', 'view_users', 'manage_users', 'view_roles', 'manage_roles'],
            $I->decode($response)['data'],
        );
    }

    public function listShowsPermissionsAndUserCounts(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveUser('m1@example.com', 'secret-pass', 'manager');
        $I->haveUser('m2@example.com', 'secret-pass', 'manager');

        $data = $I->decode($I->sendApiRequest('GET', '/api/v1/roles', null, $admin))['data'];

        $byName = array_column($data, null, 'name');
        assertSame(['admin', 'manager'], array_keys($byName));
        assertSame(2, $byName['manager']['users_count']);
        assertSame(['view_dashboard', 'edit_page'], $byName['manager']['permissions']);
        assertSame(1, $byName['admin']['users_count']);
        assertCount(6, $byName['admin']['permissions']);
        assertSame(true, $byName['admin']['read_only']);
        assertSame(false, $byName['manager']['read_only']);
    }

    public function createAddsRoleWithPermissions(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');

        $response = $I->sendApiRequest(
            'POST',
            '/api/v1/roles',
            ['name' => 'editor', 'permissions' => ['view_dashboard', 'edit_page']],
            $admin,
        );

        assertSame(201, $response->getStatusCode());
        $id = $I->decode($response)['data']['id'];
        $list = $I->decode($I->sendApiRequest('GET', '/api/v1/roles', null, $admin))['data'];
        $editor = array_column($list, null, 'name')['editor'];
        assertSame($id, $editor['id']);
        assertSame(['view_dashboard', 'edit_page'], $editor['permissions']);
        assertSame(0, $editor['users_count']);
    }

    public function createRejectsDuplicateName(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->sendApiRequest('POST', '/api/v1/roles', ['name' => 'editor', 'permissions' => []], $admin);

        $response = $I->sendApiRequest('POST', '/api/v1/roles', ['name' => 'editor', 'permissions' => []], $admin);

        assertSame(400, $response->getStatusCode());
        assertSame('Role "editor" already exists.', $I->decode($response)['error_message']);
    }

    public function createRejectsInvalidInput(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');

        $empty = $I->sendApiRequest('POST', '/api/v1/roles', ['name' => '', 'permissions' => []], $admin);
        $unknown = $I->sendApiRequest('POST', '/api/v1/roles', ['name' => 'x', 'permissions' => ['fly']], $admin);

        assertSame(422, $empty->getStatusCode());
        assertSame(422, $unknown->getStatusCode());
        assertSame(1, $I->countRows('roles'));
    }

    public function updateRenamesAndChangesPermissions(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $id = $this->createRole($I, $admin, 'editor', ['view_dashboard']);

        $response = $I->sendApiRequest(
            'PUT',
            '/api/v1/roles/' . $id,
            ['name' => 'writer', 'permissions' => ['edit_page']],
            $admin,
        );

        assertSame(200, $response->getStatusCode());
        $list = $I->decode($I->sendApiRequest('GET', '/api/v1/roles', null, $admin))['data'];
        assertCount(2, $list);
        assertSame(['edit_page'], array_column($list, null, 'name')['writer']['permissions']);
    }

    public function updateRejectsNameTakenByAnotherRole(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $this->createRole($I, $admin, 'one');
        $id = $this->createRole($I, $admin, 'two');

        $response = $I->sendApiRequest('PUT', '/api/v1/roles/' . $id, ['name' => 'one', 'permissions' => []], $admin);

        assertSame(400, $response->getStatusCode());
    }

    public function updateKeepingTheSameNameIsAllowed(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $id = $this->createRole($I, $admin, 'one');

        $response = $I->sendApiRequest(
            'PUT',
            '/api/v1/roles/' . $id,
            ['name' => 'one', 'permissions' => ['edit_page']],
            $admin,
        );

        assertSame(200, $response->getStatusCode());
    }

    public function updateOfUnknownRoleIsNotFound(ApiTester $I): void
    {
        $response = $I->sendApiRequest(
            'PUT',
            '/api/v1/roles/' . self::UNKNOWN_ID,
            ['name' => 'x', 'permissions' => []],
            $I->haveSessionFor('admin'),
        );

        assertSame(404, $response->getStatusCode());
    }

    public function adminRoleCannotBeEditedOrDeleted(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $url = '/api/v1/roles/' . $I->roleId('admin');

        $update = $I->sendApiRequest('PUT', $url, ['name' => 'boss', 'permissions' => []], $admin);
        $delete = $I->sendApiRequest('DELETE', $url, null, $admin);

        assertSame(400, $update->getStatusCode());
        assertSame('The Admin Role is read-only.', $I->decode($update)['error_message']);
        assertSame(400, $delete->getStatusCode());
        assertSame('The Admin Role is read-only.', $I->decode($delete)['error_message']);
        assertSame(6, $I->countRows('role_permissions'));
    }

    public function visiterPermissionsCanBeEditedButItCannotBeDeletedOrRenamed(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveRole('visiter');
        $url = '/api/v1/roles/' . $I->roleId('visiter');

        $update = $I->sendApiRequest('PUT', $url, ['name' => 'visiter', 'permissions' => ['view_dashboard']], $admin);
        $rename = $I->sendApiRequest('PUT', $url, ['name' => 'guest', 'permissions' => []], $admin);
        $delete = $I->sendApiRequest('DELETE', $url, null, $admin);

        assertSame(200, $update->getStatusCode());
        assertSame(400, $rename->getStatusCode());
        assertSame(400, $delete->getStatusCode());
        assertSame('The Visiter Role cannot be deleted.', $I->decode($delete)['error_message']);
        $list = $I->decode($I->sendApiRequest('GET', '/api/v1/roles', null, $admin))['data'];
        assertSame(['view_dashboard'], array_column($list, null, 'name')['visiter']['permissions']);
    }

    public function roleInUseCannotBeDeleted(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $I->haveUser('m1@example.com', 'secret-pass', 'manager');
        $I->haveUser('m2@example.com', 'secret-pass', 'manager');

        $response = $I->sendApiRequest('DELETE', '/api/v1/roles/' . $I->roleId('manager'), null, $admin);

        assertSame(400, $response->getStatusCode());
        assertSame(
            'Role "manager" cannot be deleted: it is held by 2 users.',
            $I->decode($response)['error_message'],
        );
        assertSame(2, $I->countRows('roles'));
    }

    public function unusedRoleCanBeDeleted(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $id = $this->createRole($I, $admin, 'editor', ['edit_page']);

        $response = $I->sendApiRequest('DELETE', '/api/v1/roles/' . $id, null, $admin);

        assertSame(200, $response->getStatusCode());
        assertSame(1, $I->countRows('roles'));
        assertSame(6, $I->countRows('role_permissions'));
    }

    public function permissionChangesApplyToTheNextRequestOfUsersWithThatRole(ApiTester $I): void
    {
        $admin = $I->haveSessionFor('admin');
        $manager = $I->haveSessionFor('manager');
        $page = ['title' => 'T', 'slug' => 't', 'content' => ''];
        assertSame(201, $I->sendApiRequest('POST', '/api/v1/pages', $page, $manager)->getStatusCode());

        $I->sendApiRequest(
            'PUT',
            '/api/v1/roles/' . $I->roleId('manager'),
            ['name' => 'manager', 'permissions' => ['view_dashboard']],
            $admin,
        );

        $page['slug'] = 'u';
        assertSame(403, $I->sendApiRequest('POST', '/api/v1/pages', $page, $manager)->getStatusCode());
        $me = $I->decode($I->sendApiRequest('GET', '/api/v1/auth/me', null, $manager))['data'];
        assertContains('view_dashboard', $me['permissions']);
        assertNotContains('edit_page', $me['permissions']);
    }

    /**
     * @param array<string, string> $session
     * @param list<string> $permissions
     */
    private function createRole(ApiTester $I, array $session, string $name, array $permissions = []): string
    {
        $response = $I->sendApiRequest('POST', '/api/v1/roles', ['name' => $name, 'permissions' => $permissions], $session);

        return $I->decode($response)['data']['id'];
    }
}
