<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application;

use App\Application\Page\CreatePage\Command;
use App\Application\Page\CreatePage\Handler;
use App\Application\Page\Exception\PageSlugAlreadyExistsException;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Value\PageId;
use App\Domain\Page\Repository\PageRepositoryInterface;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Yiisoft\ErrorHandler\Exception\UserException;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertSame;

final class CreatePageHandlerTest extends Unit
{
    public function testCreatesPageWithUuidV7AndClockTime(): void
    {
        $repository = new InMemoryPageRepository();
        $now = new DateTimeImmutable('2026-11-01 12:00:00');

        $result = new Handler($repository, new FixedClock($now))->handle(
            new Command(new PageTitle('About'), new PageSlug('about'), 'Body'),
        );

        assertCount(1, $repository->pages);
        $page = $repository->pages[0];
        assertSame($result->id->toString(), $page->id->toString());
        assertSame(7, $result->id->uuid->getFields()->getVersion());
        assertSame('About', $page->title->toString());
        assertSame('Body', $page->content);
        assertEquals($now, $page->createdAt);
    }

    public function testRejectsDuplicateSlug(): void
    {
        $handler = new Handler(new InMemoryPageRepository(), new FixedClock(new DateTimeImmutable()));
        $command = new Command(new PageTitle('A'), new PageSlug('same'), '');
        $handler->handle($command);

        $this->expectException(PageSlugAlreadyExistsException::class);
        $handler->handle($command);
    }
}

final class FixedClock implements ClockInterface
{
    public function __construct(private readonly DateTimeImmutable $now) {}

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }
}

final class InMemoryPageRepository implements PageRepositoryInterface
{
    /** @var list<Page> */
    public array $pages = [];

    public function getOrUserException(PageId $id): Page
    {
        foreach ($this->pages as $page) {
            if ($page->id->isEqualTo($id)) {
                return $page;
            }
        }
        throw new UserException('Page not found.');
    }

    public function hasBySlug(PageSlug $slug): bool
    {
        return array_any($this->pages, static fn(Page $p) => $p->slug->toString() === $slug->toString());
    }

    public function add(Page $page): void
    {
        $this->pages[] = $page;
    }
}
