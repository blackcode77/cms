<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\Update;

use App\Application\Client\Exception\ClientNotFoundException;
use App\Application\Client\UpdateClient\Command;
use App\Application\Client\UpdateClient\Handler;
use App\Domain\Client\Value\ClientId;
use App\Presentation\Api\Client\Shared\ClientEditInput;
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

    public function __invoke(ClientEditInput $input): ResponseInterface
    {
        $id = ClientId::tryFromString((string) $this->currentRoute->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('Клиент не найден.');
        }

        try {
            $this->handler->handle(new Command($id, $input->phone(), $input->details(), trim($input->comment)));
        } catch (ClientNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success(['id' => $id->toString()]);
    }
}
