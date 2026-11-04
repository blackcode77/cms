<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\View;

use App\Domain\Order\Value\OrderId;
use App\Presentation\Api\Order\Shared\OrderPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Order\OrderReader;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private OrderReader $reader,
        private CurrentRoute $currentRoute,
    ) {}

    public function __invoke(): ResponseInterface
    {
        $id = OrderId::tryFromString((string) $this->currentRoute->getArgument('id'));
        $order = $id === null ? null : $this->reader->get($id);
        if ($order === null) {
            return $this->responseFactory->notFound('Заказ не найден.');
        }

        return $this->responseFactory->success($order, new OrderPresenter());
    }
}
