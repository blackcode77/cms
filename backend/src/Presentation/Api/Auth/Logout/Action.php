<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Logout;

use App\Application\Auth\Logout\Handler;
use App\Presentation\Api\Auth\Shared\SessionCookies;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        if ($token !== null) {
            $this->handler->handle($token);
        }

        return $this->cookies->clear($this->responseFactory->success());
    }
}
