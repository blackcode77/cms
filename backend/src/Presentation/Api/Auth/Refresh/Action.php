<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Refresh;

use App\Application\Auth\Refresh\Handler;
use App\Presentation\Api\Auth\Shared\SessionCookies;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Status;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private SessionCookies $cookies,
        private Handler $handler,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $token = $this->cookies->refreshToken($request);
        $session = $token === null ? null : $this->handler->handle($token);
        if ($session === null) {
            return $this->cookies->clear(
                $this->responseFactory->fail('Not authenticated.', httpCode: Status::UNAUTHORIZED),
            );
        }

        return $this->cookies->attach($this->responseFactory->success(), $session);
    }
}
