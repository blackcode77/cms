<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\AssignableRoles;

use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\User\UserReader;
use Psr\Http\Message\ResponseInterface;

/**
 * Roles offered in the Users form (all except Visiter).
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserReader $userReader,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            array_map(
                static fn($role) => ['id' => $role->id->toString(), 'name' => $role->name],
                $this->userReader->assignableRoles(),
            ),
        );
    }
}
