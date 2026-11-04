<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\PageContent;

use App\Domain\PageContent\Value\ColorHex;
use App\Domain\PageContent\Value\RouteStops;
use Codeception\Test\Unit;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class RouteStopsTest extends Unit
{
    public function testNormalizesValidStops(): void
    {
        $value = json_encode([
            ['x' => 12.345, 'y' => 0, 'label' => "  Шанхай\r\nпорт ", 'side' => 'left', 'extra' => 'dropped'],
            ['x' => 100, 'y' => 55.5, 'label' => 'Владивосток', 'side' => 'right'],
        ], JSON_THROW_ON_ERROR);

        assertSame(
            '[{"x":12.3,"y":0.0,"label":"Шанхай\nпорт","side":"left"},{"x":100.0,"y":55.5,"label":"Владивосток","side":"right"}]',
            RouteStops::normalize($value),
        );
    }

    /**
     * @dataProvider badStops
     */
    public function testRejectsBadStops(string $value): void
    {
        assertNull(RouteStops::normalize($value));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function badStops(): array
    {
        $stop = ['x' => 10, 'y' => 10, 'label' => 'A', 'side' => 'top'];
        $two = static fn(array $override): string => json_encode([$stop, array_merge($stop, $override)], JSON_THROW_ON_ERROR);

        return [
            'not json' => ['[{'],
            'object' => ['{"a":1}'],
            'one stop' => [json_encode([$stop], JSON_THROW_ON_ERROR)],
            'too many' => [json_encode(array_fill(0, 21, $stop), JSON_THROW_ON_ERROR)],
            'stop not object' => ['[1, 2]'],
            'x over 100' => [$two(['x' => 100.1])],
            'negative y' => [$two(['y' => -1])],
            'string x' => [$two(['x' => '10'])],
            'missing y' => [json_encode([$stop, ['x' => 1, 'label' => 'B', 'side' => 'top']], JSON_THROW_ON_ERROR)],
            'empty label' => [$two(['label' => '  '])],
            'long label' => [$two(['label' => str_repeat('я', 61)])],
            'three lines' => [$two(['label' => "a\nb\nc"])],
            'control char' => [$two(['label' => "a\tb"])],
            'bad side' => [$two(['side' => 'center'])],
            'label not string' => [$two(['label' => ['x']])],
        ];
    }

    public function testColorIsLowercasedOrRejected(): void
    {
        assertSame('#1f6fb2', ColorHex::normalize('#1F6FB2'));
        foreach (['', '1f6fb2', '#fff', '#1f6fb2ff', 'red', '#gggggg'] as $bad) {
            assertNull(ColorHex::normalize($bad), $bad);
        }
    }
}
