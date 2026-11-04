<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\PageContent;

use App\Domain\PageContent\Value\DocumentFormat;
use App\Domain\PageContent\Value\DocumentName;
use App\Domain\PageContent\Value\DocumentUrl;
use App\Tests\Support\DocumentSamples;
use Codeception\Test\Unit;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class DocumentTest extends Unit
{
    public function testDetectsFormatsByContent(): void
    {
        assertSame(DocumentFormat::Pdf, DocumentFormat::detect(DocumentSamples::pdf()));
        assertSame(DocumentFormat::Xlsx, DocumentFormat::detect(DocumentSamples::xlsx()));
        assertSame(DocumentFormat::Docx, DocumentFormat::detect(DocumentSamples::docx()));
        assertSame(DocumentFormat::Xls, DocumentFormat::detect(DocumentSamples::xls()));
        assertSame(DocumentFormat::Doc, DocumentFormat::detect(DocumentSamples::doc()));
    }

    public function testRejectsOtherContent(): void
    {
        assertNull(DocumentFormat::detect(DocumentSamples::plainZip()));
        assertNull(DocumentFormat::detect('<html></html>'));
        assertNull(DocumentFormat::detect("MZ\x90\0"));
        assertNull(DocumentFormat::detect("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\0", 600)));
        assertNull(DocumentFormat::detect(''));
    }

    public function testCleansNames(): void
    {
        assertSame('tarify-2026', DocumentName::fromClientName('Тарифы 2026.xlsx'));
        assertSame('soc-rates-import', DocumentName::fromClientName('  SOC rates: import!.pdf'));
        assertSame('document', DocumentName::fromClientName('...pdf'));
        assertSame('document', DocumentName::fromClientName(null));
        assertSame('b', DocumentName::fromClientName('../../a/b'));
        $long = DocumentName::fromClientName(str_repeat('ab ', 100) . '.pdf');
        assertTrue(strlen($long) <= DocumentName::LENGTH_LIMIT && !str_ends_with($long, '-'));
    }

    public function testDocumentUrl(): void
    {
        assertTrue(DocumentUrl::isValid(''));
        assertTrue(DocumentUrl::isValid('/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/tarify-2026.xlsx'));
        assertFalse(DocumentUrl::isValid('/uploads/files/0193a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b/../x.pdf'));
        assertFalse(DocumentUrl::isValid('/uploads/x.pdf'));
    }
}
