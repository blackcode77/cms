<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\Create;

use App\Application\User\CreateUser\Command;
use App\Application\User\CreateUser\Handler;
use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(Input $input, ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->handler->handle(
            new Command(new UserEmail($input->email), trim($input->name), RoleId::fromString($input->role_id), Authenticate::currentUser($request)->user->id),
        );

        return $this->responseFactory
            ->success(['id' => $result->id->toString(), 'mail_sent' => $result->mailSent])
            ->withStatus(201);
    }
}
