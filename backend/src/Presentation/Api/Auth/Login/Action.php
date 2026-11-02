<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth\Login;

use App\Application\Auth\Exception\InvalidCredentialsException;
use App\Application\Auth\Login\Command;
use App\Application\Auth\Login\Handler;
use App\Domain\User\Value\UserEmail;
use App\Presentation\Api\Auth\Shared\ClientIp;
use App\Presentation\Api\Auth\Shared\SessionCookies;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use InvalidArgumentException;
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
        try {
            $email = new UserEmail($input->email);
        } catch (InvalidArgumentException) {
            // a malformed email must not be distinguishable from an unknown one
            return $this->responseFactory->fail(InvalidCredentialsException::create()->getMessage());
        }

        $session = $this->handler->handle(
            new Command($email, $input->password, ClientIp::fromRequest($request)),
        );

        return $this->cookies->attach($this->responseFactory->success(), $session);
    }
}
