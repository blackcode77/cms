<?php

declare(strict_types=1);

namespace App\Application\Page\CreatePage;

use App\Domain\Page\Value\PageId;

final readonly class Result
{
    public function __construct(
        public PageId $id,
    ) {}
}
