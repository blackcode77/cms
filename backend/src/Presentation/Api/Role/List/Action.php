<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\List;

use App\Presentation\Api\Shared\ResponseFactory\Presenter\CollectionPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Role\RoleReader;
use Psr\Http\Message\ResponseInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private RoleReader $roleReader,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            $this->roleReader->find(),
            new CollectionPresenter(new RolePresenter()),
        );
    }
}
