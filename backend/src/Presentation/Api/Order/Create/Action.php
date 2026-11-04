<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Create;

use App\Application\Order\CreateOrder\Command;
use App\Application\Order\CreateOrder\Handler;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(Input $input): ResponseInterface
    {
        $result = $this->handler->handle(
            new Command(
                $input->phone(),
                $input->details(),
                $input->orderStatus(),
                $input->managerId(),
                $input->cargo(),
                trim($input->message),
                trim($input->comment),
            ),
        );

        return $this->responseFactory
            ->success([
                'id' => $result->id->toString(),
                'number' => $result->number,
                'client_id' => $result->clientId->toString(),
            ])
            ->withStatus(201);
    }
}
