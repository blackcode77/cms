<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\ChangePassword;

use App\Application\Auth\ChangePassword\Handler;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Auth\Shared\ClientIp;
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

    public function __invoke(Input $input, ServerRequestInterface $request): ResponseInterface
    {
        $session = $this->handler->handle(
            Authenticate::currentUser($request)->user,
            $input->current_password,
            $input->new_password,
            ClientIp::fromRequest($request),
        );

        return $this->cookies->attach($this->responseFactory->success(), $session);
    }
}
