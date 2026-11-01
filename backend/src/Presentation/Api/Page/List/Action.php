<?php

declare(strict_types=1);

namespace App\Presentation\Api\Page\List;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\CollectionPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Page\PageReader;
use Psr\Http\Message\ResponseInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private PageReader $pageReader,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            $this->pageReader->find(),
            new CollectionPresenter(new PagePresenter()),
        );
    }
}
