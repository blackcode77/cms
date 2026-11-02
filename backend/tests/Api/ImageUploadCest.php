<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

use function PHPUnit\Framework\assertFileExists;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertSame;

final class ImageUploadCest
{
    private const string JPEG = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
    private const string WEBP = 'UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
    private const string PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
    private const string UPLOAD = '/api/v1/uploads/image';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function uploadWithoutSessionReturns401(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::UPLOAD, null, [], [], [], ['file' => base64_decode(self::PNG)]);

        assertSame(401, $response->getStatusCode());
    }

    public function uploadWithoutEditPageReturns403(ApiTester $I): void
    {
        $response = $I->sendApiRequest(
            'POST',
            self::UPLOAD,
            null,
            $I->haveSessionFor('viewer'),
            [],
            [],
            ['file' => base64_decode(self::PNG)],
        );

        assertSame(403, $response->getStatusCode());
    }

    /**
     * @dataProvider validImages
     */
    public function validImageIsStoredAndNamedByRealFormat(ApiTester $I, \Codeception\Example $example): void
    {
        $response = $I->sendApiRequest(
            'POST',
            self::UPLOAD,
            null,
            $I->haveSessionFor('manager'),
            [],
            [],
            ['file' => base64_decode($example['content'])],
        );

        assertSame(201, $response->getStatusCode());
        $url = $I->decode($response)['data']['url'];
        assertMatchesRegularExpression('#^/uploads/[0-9a-f-]{36}\.' . $example['ext'] . '$#', $url);
        assertFileExists(dirname(__DIR__, 2) . '/runtime/test-uploads/' . basename($url));
    }

    /**
     * @return array<string, array{content: string, ext: string}>
     */
    protected function validImages(): array
    {
        return [
            'jpeg' => ['content' => self::JPEG, 'ext' => 'jpg'],
            'png' => ['content' => self::PNG, 'ext' => 'png'],
            'webp' => ['content' => self::WEBP, 'ext' => 'webp'],
        ];
    }

    public function rejectsSvgOversizedAndNonImages(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $cases = [
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
            'text renamed to jpg' => 'just text, not an image',
            'html' => '<html><script>alert(1)</script></html>',
            'oversized' => base64_decode(self::PNG) . str_repeat('0', 5 * 1024 * 1024),
        ];

        foreach ($cases as $name => $content) {
            $response = $I->sendApiRequest('POST', self::UPLOAD, null, $session, [], [], ['file' => $content]);
            assertSame(400, $response->getStatusCode(), $name);
            assertSame('failed', $I->decode($response)['status'], $name);
        }
        assertSame([], glob(dirname(__DIR__, 2) . '/runtime/test-uploads/*') ?: []);
    }

    public function rejectsRequestWithoutFile(ApiTester $I): void
    {
        $response = $I->sendApiRequest('POST', self::UPLOAD, null, $I->haveSessionFor('manager'));

        assertSame(400, $response->getStatusCode());
    }

    public function imageValueAcceptsOnlyOwnUrls(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $patch = static fn(string $value): array => ['path' => 'hero.image', 'type' => 'image', 'value' => $value];

        foreach (['/uploads/0190a1b2-c3d4.jpg', '/img/hero.jpg', '/img/sub/dir/a_b-c.webp'] as $ok) {
            $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $patch($ok), $session);
            assertSame(200, $response->getStatusCode(), $ok);
        }

        $bad = [
            'https://evil.example/x.jpg',
            '//evil.example/x.jpg',
            'javascript:alert(1)',
            'data:image/svg+xml;base64,AAAA',
            '/uploads/../../etc/passwd',
            '/other/x.jpg',
            '/uploads/x.jpg?x=1',
            '/uploads/',
            '',
        ];
        foreach ($bad as $value) {
            $response = $I->sendApiRequest('PATCH', '/api/v1/page-content/home', $patch($value), $session);
            assertSame(400, $response->getStatusCode(), $value);
        }

        $home = $I->decode($I->sendApiRequest('GET', '/api/v1/page-content?keys=home'))['data']['home'];
        assertSame('/img/sub/dir/a_b-c.webp', $home['hero.image']['value']);
    }
}
