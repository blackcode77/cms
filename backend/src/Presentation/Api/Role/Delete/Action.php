<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Delete;

use App\Application\Role\DeleteRole\Command;
use App\Application\Role\DeleteRole\Handler;
use App\Application\Role\Exception\RoleNotFoundException;
use App\Domain\Role\Value\RoleId;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $route,
    ) {}

    public function __invoke(): ResponseInterface
    {
        $id = RoleId::tryFromString($this->route->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('Role not found.');
        }

        try {
            $this->handler->handle(new Command($id));
        } catch (RoleNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success();
    }
}
