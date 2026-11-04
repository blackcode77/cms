<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Formats accepted for Documents, detected from the file's real content (never from its name).
 * Office Open XML files are ZIP archives and Office binary files are OLE2 containers; both are recognized
 * by the names of the parts they must contain, which appear as plain (or UTF-16LE) text in the file.
 */
enum DocumentFormat: string
{
    case Pdf = 'pdf';
    case Xlsx = 'xlsx';
    case Docx = 'docx';
    case Xls = 'xls';
    case Doc = 'doc';

    private const string ZIP_SIGNATURE = "PK\x03\x04";
    private const string OLE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

    public static function detect(string $bytes): ?self
    {
        if (str_starts_with($bytes, '%PDF-')) {
            return self::Pdf;
        }
        if (str_starts_with($bytes, self::ZIP_SIGNATURE) && str_contains($bytes, '[Content_Types].xml')) {
            return match (true) {
                str_contains($bytes, 'xl/workbook') => self::Xlsx,
                str_contains($bytes, 'word/document') => self::Docx,
                default => null,
            };
        }
        if (str_starts_with($bytes, self::OLE_SIGNATURE)) {
            return match (true) {
                str_contains($bytes, self::utf16('Workbook')), str_contains($bytes, self::utf16('Book')) => self::Xls,
                str_contains($bytes, self::utf16('WordDocument')) => self::Doc,
                default => null,
            };
        }

        return null;
    }

    public function extension(): string
    {
        return $this->value;
    }

    private static function utf16(string $ascii): string
    {
        return implode("\0", str_split($ascii)) . "\0";
    }
}
