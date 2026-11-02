<?php

declare(strict_types=1);

namespace App\Domain\PageContent\Repository;

use App\Domain\PageContent\Value\ContentPath;
use App\Domain\PageContent\Value\ElementPatch;
use App\Domain\PageContent\Value\PageKey;
use DateTimeImmutable;

interface PageContentRepositoryInterface
{
    /**
     * Applies the patch to one element atomically, so concurrent edits of other elements are never lost.
     */
    public function patchElement(PageKey $key, ContentPath $path, ElementPatch $patch, DateTimeImmutable $now): void;
}
