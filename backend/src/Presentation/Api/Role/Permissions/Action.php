<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Permissions;

use App\Domain\Role\Value\Permission;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * The fixed Permission catalog that drives the Role editor's checkboxes.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            array_map(static fn(Permission $p): string => $p->value, Permission::cases()),
        );
    }
}
