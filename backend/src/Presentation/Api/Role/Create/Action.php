<?php

declare(strict_types=1);

namespace App\Presentation\Api\Role\Create;

use App\Application\Role\CreateRole\Command;
use App\Application\Role\CreateRole\Handler;
use App\Domain\Role\Value\RoleName;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Webmozart\Assert\Assert;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        Assert::stringNotEmpty($input->name);

        $result = $this->handler->handle(
            new Command(new RoleName($input->name), $input->permissionList()),
        );

        return $this->responseFactory
            ->success(['id' => $result->id->toString()])
            ->withStatus(201);
    }
}
