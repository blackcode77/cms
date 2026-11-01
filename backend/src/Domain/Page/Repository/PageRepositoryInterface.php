<?php

declare(strict_types=1);

namespace App\Domain\Page\Repository;

use App\Domain\Page\Entity\Page;
use App\Domain\Page\Value\PageId;
use App\Domain\Page\Value\PageSlug;
use Yiisoft\ErrorHandler\Exception\UserException;

interface PageRepositoryInterface
{
    /**
     * @throws UserException
     */
    public function getOrUserException(PageId $id): Page;

    public function hasBySlug(PageSlug $slug): bool;

    public function add(Page $page): void;
}
