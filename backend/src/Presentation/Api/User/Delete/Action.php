<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\Delete;

use App\Application\User\DeleteUser\Command;
use App\Application\User\DeleteUser\Handler;
use App\Application\User\Exception\UserNotFoundException;
use App\Domain\User\Value\UserId;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $currentRoute,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $id = UserId::fromString((string) $this->currentRoute->getArgument('id'));
        } catch (InvalidArgumentException) {
            throw UserNotFoundException::create();
        }

        $this->handler->handle(new Command($id, Authenticate::currentUser($request)->user->id));

        return $this->responseFactory->success(['id' => $id->toString()]);
    }
}
