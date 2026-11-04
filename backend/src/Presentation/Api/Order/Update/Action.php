<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Update;

use App\Application\Order\Exception\OrderNotFoundException;
use App\Application\Order\UpdateOrder\Command;
use App\Application\Order\UpdateOrder\Handler;
use App\Domain\Order\Value\OrderId;
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
        $id = OrderId::tryFromString((string) $this->currentRoute->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('Заказ не найден.');
        }

        try {
            $this->handler->handle(
                new Command(
                    $id,
                    $input->orderStatus(),
                    $input->managerId(),
                    $input->cargo(),
                    trim($input->message),
                    trim($input->comment),
                ),
            );
        } catch (OrderNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success(['id' => $id->toString()]);
    }
}
