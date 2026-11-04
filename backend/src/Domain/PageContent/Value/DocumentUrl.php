<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

/**
 * Value of a `file` Content element: the public path of an uploaded Document,
 * `/uploads/files/<uuid>/<name>.<ext>`. Empty means "no file yet".
 */
final readonly class DocumentUrl
{
    public const string PATTERN = '~^/uploads/files/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/[a-z0-9]+(?:-[a-z0-9]+)*\.(?:pdf|xlsx|xls|docx|doc)$~';

    public static function isValid(string $value): bool
    {
        return $value === '' || preg_match(self::PATTERN, $value) === 1;
    }
}
