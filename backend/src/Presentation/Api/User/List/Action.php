<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\List;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\CollectionPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\User\UserReader;
use Psr\Http\Message\ResponseInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserReader $userReader,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            $this->userReader->find(),
            new CollectionPresenter(new UserPresenter()),
        );
    }
}
