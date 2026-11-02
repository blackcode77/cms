<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Me;

use App\Application\Auth\AccessTokenResolver;
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
        private AccessTokenResolver $resolver,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $current = $this->resolver->resolve($this->cookies->accessToken($request));
        if ($current === null) {
            return $this->responseFactory->fail('Not authenticated.', httpCode: Status::UNAUTHORIZED);
        }

        return $this->responseFactory->success(new Presenter()->present($current));
    }
}
