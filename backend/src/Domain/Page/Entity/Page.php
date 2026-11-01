<?php

declare(strict_types=1);

namespace App\Domain\Page\Entity;

use App\Domain\Page\Value\PageId;
use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;
use DateTimeImmutable;

final class Page
{
    public readonly PageId $id;
    public private(set) PageTitle $title;
    public private(set) PageSlug $slug;
    public private(set) string $content;
    public readonly DateTimeImmutable $createdAt;

    public function __construct(
        PageId $id,
        PageTitle $title,
        PageSlug $slug,
        string $content,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function changeTitle(PageTitle $title): void
    {
        $this->title = $title;
    }

    public function changeContent(string $content): void
    {
        $this->content = $content;
    }
}
