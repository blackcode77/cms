<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\PageContent;

use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ContentType;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\PageKey;
use Codeception\Test\Unit;
use InvalidArgumentException;

use function PHPUnit\Framework\assertSame;

final class ValueObjectsTest extends Unit
{
    public function testAcceptsValidPathAndKey(): void
    {
        assertSame('header.phone_number_one', new ContentPath('header.phone_number_one')->toString());
        assertSame('blocks.0.title', new ContentPath('blocks.0.title')->toString());
        assertSame('site', new PageKey('site')->toString());
        assertSame('dangerous-goods', new PageKey('dangerous-goods')->toString());
    }

    /**
     * @dataProvider badPaths
     */
    public function testRejectsBadPath(string $path): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentPath($path);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function badPaths(): array
    {
        return [
            'uppercase' => ['Header.title'],
            'leading dot' => ['.title'],
            'trailing dot' => ['title.'],
            'double dot' => ['a..b'],
            'space' => ['a b'],
            'brackets' => ['a[0]'],
            'too long' => [str_repeat('a', 101)],
        ];
    }

    public function testRejectsBadKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PageKey('Not A Key');
    }

    public function testPatchNeedsTypeForValueAndSomethingToChange(): void
    {
        assertSame(['enabled' => false], new ElementPatch(null, null, false)->toArray());
        assertSame(
            ['type' => 'text', 'value' => 'x'],
            new ElementPatch(ContentType::Text, 'x', null)->toArray(),
        );

        $this->expectException(InvalidArgumentException::class);
        new ElementPatch(null, 'x', null);
    }

    public function testEmptyPatchIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ElementPatch(ContentType::Text, null, null);
    }
}
