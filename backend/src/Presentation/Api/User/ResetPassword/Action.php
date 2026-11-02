<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\ResetPassword;

use App\Application\User\Exception\UserNotFoundException;
use App\Application\User\ResetPassword\Handler;
use App\Domain\User\Value\UserId;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $route,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = UserId::tryFromString($this->route->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('User not found.');
        }

        try {
            $mailSent = $this->handler->handle($id, Authenticate::currentUser($request)->user->id);
        } catch (UserNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success(['mail_sent' => $mailSent]);
    }
}
