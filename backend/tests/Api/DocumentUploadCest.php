<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\Support\ApiTester;
use App\Tests\Support\DocumentSamples;
use Codeception\Example;

use function PHPUnit\Framework\assertFileExists;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringEqualsFile;

final class DocumentUploadCest
{
    private const string UPLOAD = '/api/v1/uploads/file';

    public function _before(ApiTester $I): void
    {
        $I->cleanDatabase();
    }

    public function requiresSessionAndEditPage(ApiTester $I): void
    {
        $file = ['file' => [DocumentSamples::pdf(), 'a.pdf']];

        assertSame(401, $I->sendApiRequest('POST', self::UPLOAD, null, [], [], [], $file)->getStatusCode());
        assertSame(403, $I->sendApiRequest('POST', self::UPLOAD, null, $I->haveSessionFor('viewer'), [], [], $file)->getStatusCode());
    }

    /**
     * @dataProvider documents
     */
    public function storesDocumentUnderCleanedOriginalName(ApiTester $I, Example $example): void
    {
        $content = DocumentSamples::{$example['sample']}();

        $response = $I->sendApiRequest(
            'POST',
            self::UPLOAD,
            null,
            $I->haveSessionFor('manager'),
            [],
            [],
            ['file' => [$content, $example['name']]],
        );

        assertSame(201, $response->getStatusCode());
        $data = $I->decode($response)['data'];
        assertMatchesRegularExpression('#^/uploads/files/[0-9a-f-]{36}/' . preg_quote($example['stored'], '#') . '$#', $data['url']);
        assertSame(strlen($content), $data['size']);
        $path = dirname(__DIR__, 2) . '/runtime/test-uploads/' . substr($data['url'], strlen('/uploads/'));
        assertFileExists($path);
        assertStringEqualsFile($path, $content);
    }

    /**
     * @return array<string, array{sample: string, name: string, stored: string}>
     */
    protected function documents(): array
    {
        return [
            'pdf, cyrillic name' => ['sample' => 'pdf', 'name' => 'Расписание NEWSTRIVER (ноябрь).pdf', 'stored' => 'raspisaniye-newstriver-noyabr.pdf'],
            'xlsx' => ['sample' => 'xlsx', 'name' => 'Тарифы 2026.xlsx', 'stored' => 'tarify-2026.xlsx'],
            'docx' => ['sample' => 'docx', 'name' => 'guide.docx', 'stored' => 'guide.docx'],
            'xls named by real format' => ['sample' => 'xls', 'name' => 'old.doc', 'stored' => 'old.xls'],
            'doc' => ['sample' => 'doc', 'name' => 'Памятка.doc', 'stored' => 'pamyatka.doc'],
            'name without letters' => ['sample' => 'pdf', 'name' => '!!!.pdf', 'stored' => 'document.pdf'],
        ];
    }

    public function rejectsOtherFormatsAndOversized(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $cases = [
            'plain zip' => [DocumentSamples::plainZip(), 'a.xlsx'],
            'html' => ['<html><script>alert(1)</script></html>', 'a.pdf'],
            'svg' => ['<svg xmlns="http://www.w3.org/2000/svg"/>', 'a.pdf'],
            'png' => [base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='), 'a.pdf'],
            'exe' => ["MZ\x90\0" . str_repeat("\0", 60), 'a.doc'],
            'oversized' => [DocumentSamples::pdf() . str_repeat('0', 20 * 1024 * 1024), 'a.pdf'],
        ];

        foreach ($cases as $name => $file) {
            $response = $I->sendApiRequest('POST', self::UPLOAD, null, $session, [], [], ['file' => $file]);
            assertSame(400, $response->getStatusCode(), $name);
        }
        assertSame([], glob(dirname(__DIR__, 2) . '/runtime/test-uploads/files/*') ?: []);
    }

    public function rejectsRequestWithoutFile(ApiTester $I): void
    {
        assertSame(400, $I->sendApiRequest('POST', self::UPLOAD, null, $I->haveSessionFor('manager'))->getStatusCode());
    }

    public function fileValueAcceptsOnlyUploadedDocuments(ApiTester $I): void
    {
        $session = $I->haveSessionFor('manager');
        $patch = static fn(string $value): array => ['path' => 'doc.file', 'type' => 'file', 'value' => $value];
        $ok = ['', '/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/tarify-2026.xlsx'];
        $bad = [
            '/uploads/0193a1b2.jpg',
            '/img/x.pdf',
            'https://evil.example/x.pdf',
            '/uploads/files/../../etc/passwd',
            '/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/x.exe',
            '/uploads/files/not-a-uuid/x.pdf',
        ];

        foreach ($ok as $value) {
            assertSame(200, $I->sendApiRequest('PATCH', '/api/v1/page-content/schedules', $patch($value), $session)->getStatusCode(), $value);
        }
        foreach ($bad as $value) {
            assertSame(400, $I->sendApiRequest('PATCH', '/api/v1/page-content/schedules', $patch($value), $session)->getStatusCode(), $value);
        }
    }
}
