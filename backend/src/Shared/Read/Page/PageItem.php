<?php

declare(strict_types=1);

namespace App\Shared\Read\Page;

use App\Domain\Page\Value\PageId;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
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
