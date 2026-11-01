<?php

declare(strict_types=1);

namespace App\Application\Page\CreatePage;

use App\Application\Page\Exception\PageSlugAlreadyExistsException;
use App\Domain\Page\Entity\Page;
use App\Domain\Page\Value\PageId;
use App\Domain\Page\Repository\PageRepositoryInterface;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

final readonly class Handler
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws PageSlugAlreadyExistsException
     */
    public function handle(Command $command): Result
    {
        if ($this->pageRepository->hasBySlug($command->slug)) {
            throw PageSlugAlreadyExistsException::fromSlug($command->slug);
        }

        $id = new PageId(Uuid::uuid7());

        $this->pageRepository->add(
            new Page(
                $id,
                $command->title,
                $command->slug,
                $command->content,
                DateTimeImmutable::createFromInterface($this->clock->now()),
            ),
        );

        return new Result($id);
    }
}
