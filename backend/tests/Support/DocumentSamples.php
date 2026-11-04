<?php

declare(strict_types=1);

namespace App\Tests\Support;

/**
 * Minimal byte strings with the structure the document format detection looks at.
 */
final class DocumentSamples
{
    public static function pdf(): string
    {
        return "%PDF-1.7\n1 0 obj << /Type /Catalog >> endobj\ntrailer << /Root 1 0 R >>\n%%EOF\n";
    }

    public static function xlsx(): string
    {
        return self::zip(['[Content_Types].xml', 'xl/workbook.xml', 'xl/worksheets/sheet1.xml']);
    }

    public static function docx(): string
    {
        return self::zip(['[Content_Types].xml', 'word/document.xml']);
    }

    public static function plainZip(): string
    {
        return self::zip(['readme.txt', 'evil.exe']);
    }

    public static function xls(): string
    {
        return self::ole('Workbook');
    }

    public static function doc(): string
    {
        return self::ole('WordDocument');
    }

    /**
     * ZIP local file headers carry entry names in plain text; contents do not matter for detection.
     *
     * @param list<string> $names
     */
    private static function zip(array $names): string
    {
        $bytes = '';
        foreach ($names as $name) {
            $bytes .= "PK\x03\x04" . str_repeat("\0", 22) . pack('v', strlen($name)) . "\0\0" . $name . 'x';
        }

        return $bytes . "PK\x05\x06" . str_repeat("\0", 18);
    }

    /**
     * OLE2 signature plus a directory entry name in UTF-16LE, as Office binary files have.
     */
    private static function ole(string $stream): string
    {
        $name = mb_convert_encoding($stream, 'UTF-16LE', 'UTF-8');

        return "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\0", 504) . $name . str_repeat("\0", 64);
    }
}
