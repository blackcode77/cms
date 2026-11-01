<?php

declare(strict_types=1);

namespace App\Application\Page\CreatePage;

use App\Domain\Page\Value\PageSlug;
use App\Domain\Page\Value\PageTitle;

final readonly class Command
{
    public function __construct(
        public PageTitle $title,
        public PageSlug $slug,
        public string $content,
    ) {}
}
