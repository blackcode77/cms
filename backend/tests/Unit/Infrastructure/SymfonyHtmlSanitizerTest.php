<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use App\Infrastructure\SymfonyHtmlSanitizer;
use Codeception\Test\Unit;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

final class SymfonyHtmlSanitizerTest extends Unit
{
    public function testKeepsSafeMarkup(): void
    {
        $html = '<p>Hello <b>world</b> <a href="https://example.com">link</a></p>';

        assertSame($html, new SymfonyHtmlSanitizer()->sanitize($html));
    }

    public function testKeepsRelativeLinksAndMailto(): void
    {
        $out = new SymfonyHtmlSanitizer()->sanitize('<a href="/contacts">c</a><a href="mailto:a@b.ru">m</a>');

        assertStringContainsString('href="/contacts"', $out);
        assertStringContainsString('href="mailto:a&#64;b.ru"', $out);
    }

    /**
     * @dataProvider dangerous
     */
    public function testStripsDangerousMarkup(string $html, string $forbidden): void
    {
        assertStringNotContainsString($forbidden, new SymfonyHtmlSanitizer()->sanitize($html));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function dangerous(): array
    {
        return [
            'script' => ['<script>alert(1)</script>x', 'script'],
            'inline handler' => ['<p onclick="a()">x</p>', 'onclick'],
            'img onerror' => ['<img src="x" onerror="a()">', 'onerror'],
            'javascript url' => ['<a href="javascript:alert(1)">x</a>', 'javascript:'],
            'iframe' => ['<iframe src="https://evil.example"></iframe>', 'iframe'],
            'style' => ['<style>body{display:none}</style>', 'style'],
        ];
    }
}
