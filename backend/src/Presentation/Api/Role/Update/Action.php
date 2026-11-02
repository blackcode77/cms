<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Update;

use App\Application\Role\Exception\RoleNotFoundException;
use App\Application\Role\UpdateRole\Command;
use App\Application\Role\UpdateRole\Handler;
use App\Domain\Role\Value\RoleId;
use App\Domain\Role\Value\RoleName;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $route,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        Assert::stringNotEmpty($input->name);

        $id = RoleId::tryFromString($this->route->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('Role not found.');
        }

        try {
            $this->handler->handle(new Command($id, new RoleName($input->name), $input->permissionList()));
        } catch (RoleNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success();
    }
}
