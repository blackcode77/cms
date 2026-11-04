<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Value;

use Transliterator;

/**
 * File name a visitor gets when downloading a Document: the uploader's file name transliterated to latin and
 * reduced to lowercase words joined by dashes, e.g. «Расписание (ноябрь).pdf» → `raspisanie-noyabr`.
 */
final readonly class DocumentName
{
    public const int LENGTH_LIMIT = 80;
    private const string FALLBACK = 'document';

    public static function fromClientName(?string $clientName): string
    {
        $base = pathinfo($clientName ?? '', PATHINFO_FILENAME);
        $latin = Transliterator::create('Russian-Latin/BGN; Any-Latin; Latin-ASCII; Lower()')?->transliterate($base);
        $slug = trim((string) preg_replace('/[^a-z0-9]+/', '-', is_string($latin) ? $latin : ''), '-');
        $slug = rtrim(substr($slug, 0, self::LENGTH_LIMIT), '-');

        return $slug === '' ? self::FALLBACK : $slug;
    }
}
