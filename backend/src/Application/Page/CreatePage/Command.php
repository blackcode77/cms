<?php

declare(strict_types=1);

namespace App\Application\Page\CreatePage;

use App\Domain\Page\PageSlug;
use App\Domain\Page\PageTitle;

final readonly class Command
{
    public function __construct(
        public PageTitle $title,
        public PageSlug $slug,
        public string $content,
    ) {}
}
