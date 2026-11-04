<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Create;

use App\Application\Client\CreateClient\Command;
use App\Application\Client\CreateClient\Handler;
use App\Presentation\Api\Client\Shared\ClientEditInput;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(ClientEditInput $input): ResponseInterface
    {
        $id = $this->handler->handle(new Command($input->phone(), $input->details(), trim($input->comment)));

        return $this->responseFactory->success(['id' => $id->toString()])->withStatus(201);
    }
}
