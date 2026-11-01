<?php

declare(strict_types=1);

namespace App\Presentation\Api\Page\List;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\PresenterInterface;
use App\Shared\Read\Page\PageItem;
use DateTimeInterface;

/**
 * @implements PresenterInterface<PageItem>
 */
final readonly class PagePresenter implements PresenterInterface
{
    public function present(mixed $value): array
    {
        return [
            'id' => $value->id->toString(),
            'title' => $value->title->toString(),
            'slug' => $value->slug->toString(),
            'created_at' => $value->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}
