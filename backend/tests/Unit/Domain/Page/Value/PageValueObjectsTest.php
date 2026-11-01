<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Page\Value;

use App\Domain\Page\Value\PageId;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
use Codeception\Test\Unit;
use InvalidArgumentException;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class PageValueObjectsTest extends Unit
{
    public function testValidSlug(): void
    {
        assertSame('about-us-2', new PageSlug('about-us-2')->toString());
    }

    public function testInvalidSlugs(): void
    {
        foreach (['', 'About', 'a b', '-a', 'a-', 'a--b', str_repeat('a', 101)] as $value) {
            try {
                new PageSlug($value);
                $this->fail('Slug "' . $value . '" must be rejected.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testTitleLimits(): void
    {
        assertSame(str_repeat('a', 200), new PageTitle(str_repeat('a', 200))->toString());

        $this->expectException(InvalidArgumentException::class);
        new PageTitle(str_repeat('a', 201));
    }

    public function testEmptyTitleRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PageTitle('');
    }

    public function testPageIdFromString(): void
    {
        $uuid = '0192f3a0-7c1e-7000-8000-000000000001';

        assertSame($uuid, PageId::fromString($uuid)->toString());
        assertTrue(PageId::fromString($uuid)->isEqualTo(PageId::fromString($uuid)));
        assertNull(PageId::tryFromString('not-a-uuid'));
    }
}
