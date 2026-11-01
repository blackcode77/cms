<?php

declare(strict_types=1);

namespace App\Shared\Read\Page;

use App\Domain\Page\PageId;
use App\Domain\Page\PageSlug;
use App\Domain\Page\PageTitle;
use DateTimeImmutable;

final readonly class PageItem
{
    public function __construct(
        public PageId $id,
        public PageTitle $title,
        public PageSlug $slug,
        public DateTimeImmutable $createdAt,
    ) {}
}
