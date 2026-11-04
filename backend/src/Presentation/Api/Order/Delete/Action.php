<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Delete;

use App\Application\Order\DeleteOrder\Command;
use App\Application\Order\DeleteOrder\Handler;
use App\Application\Order\Exception\OrderNotFoundException;
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

    public function __invoke(): ResponseInterface
    {
        $id = OrderId::tryFromString((string) $this->currentRoute->getArgument('id'));
        if ($id === null) {
            return $this->responseFactory->notFound('Заказ не найден.');
        }

        try {
            $this->handler->handle(new Command($id));
        } catch (OrderNotFoundException $e) {
            return $this->responseFactory->notFound($e->getMessage());
        }

        return $this->responseFactory->success(['id' => $id->toString()]);
    }
}
