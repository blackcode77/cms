<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\Update;

use App\Application\User\Exception\UserNotFoundException;
use App\Application\User\UpdateUser\Command;
use App\Application\User\UpdateUser\Handler;
use App\Domain\Role\Value\RoleId;
use App\Domain\User\Value\UserEmail;
use App\Domain\User\Value\UserId;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private CurrentRoute $currentRoute,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        $id = UserId::tryFromString((string) $this->currentRoute->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('User not found.');
        }

        try {
            $this->handler->handle(
                new Command(
                    $id,
                    new UserEmail($input->email),
                    trim($input->name),
                    RoleId::fromString($input->role_id),
                ),
            );
        } catch (UserNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success(['id' => $id->toString()]);
    }
}
