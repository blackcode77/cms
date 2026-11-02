<?php

declare(strict_types=1);

namespace App\Application\PageContent\UpdateElement;

use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\PageKey;

final readonly class Command
{
    public function __construct(
        public PageKey $key,
        public ContentPath $path,
        public ElementPatch $patch,
    ) {}
}
