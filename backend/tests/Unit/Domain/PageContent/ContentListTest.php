<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\PageContent;

use App\Domain\PageContent\Value\ContentList;
use App\Domain\PageContent\Value\ContentType;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\IconName;
use Codeception\Test\Unit;
use InvalidArgumentException;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class ContentListTest extends Unit
{
    public function testKeepsOrderAndMapsOnlyFieldValuesByType(): void
    {
        $list = ContentList::fromArray(
            ['title' => 'string', 'text' => 'html'],
            [['id' => 'b', 'title' => 'B', 'text' => 'x'], ['id' => 'a', 'title' => 'A']],
        );

        $mapped = $list->mapValues(static fn(ContentType $type, string $value): string => $type->value . ':' . $value);

        assertSame(
            [['id' => 'b', 'title' => 'string:B', 'text' => 'html:x'], ['id' => 'a', 'title' => 'string:A']],
            $mapped->items,
        );
        assertSame(
            ['type' => 'list', 'value' => $mapped->items],
            new ElementPatch(ContentType::List, $mapped, null)->toArray(),
        );
    }

    /**
     * @dataProvider malformed
     */
    public function testRejectsMalformedShape(mixed $fields, mixed $items): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContentList::fromArray($fields, $items);
    }

    /**
     * @return array<string, array{mixed, mixed}>
     */
    public static function malformed(): array
    {
        $fields = ['title' => 'string'];

        return [
            'fields not a map' => [['string'], []],
            'bad field name' => [['Title' => 'string'], []],
            'reserved id field' => [['id' => 'string'], []],
            'nested list' => [['title' => 'list'], []],
            'items not a list' => [$fields, ['x' => ['id' => 'a']]],
            'item not a map' => [$fields, ['x']],
            'missing id' => [$fields, [['title' => 'x']]],
            'bad id' => [$fields, [['id' => 'a b']]],
            'duplicate id' => [$fields, [['id' => 'a'], ['id' => 'a']]],
            'undeclared field' => [$fields, [['id' => 'a', 'x' => 'y']]],
            'non-string value' => [$fields, [['id' => 'a', 'title' => 1]]],
        ];
    }

    public function testScalarTypeCannotCarryList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ElementPatch(ContentType::Text, ContentList::fromArray([], []), null);
    }

    public function testListTypeCannotCarryString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ElementPatch(ContentType::List, 'x', null);
    }

    public function testIconName(): void
    {
        assertTrue(IconName::isValid('map-pin'));
        assertTrue(IconName::isValid('arrow-up-1'));
        assertFalse(IconName::isValid('Map-Pin'));
        assertFalse(IconName::isValid('i-lucide:map'));
        assertFalse(IconName::isValid('-x'));
        assertFalse(IconName::isValid(str_repeat('a', 65)));
    }
}
