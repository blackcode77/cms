<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\View;

use App\Domain\Client\Value\ClientId;
use App\Presentation\Api\Client\Shared\ClientPresenter;
use App\Presentation\Api\Order\Shared\OrderPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Client\ClientReader;
use App\Shared\Read\Order\OrderFilter;
use App\Shared\Read\Order\OrderReader;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Router\CurrentRoute;

/**
 * The Client with their Orders (newest first).
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ClientReader $clients,
        private OrderReader $orders,
        private CurrentRoute $currentRoute,
    ) {}

    public function __invoke(): ResponseInterface
    {
        $id = ClientId::tryFromString((string) $this->currentRoute->getArgument('id'));
        $client = $id === null ? null : $this->clients->get($id);
        if ($client === null) {
            return $this->responseFactory->notFound('Клиент не найден.');
        }

        $orderPresenter = new OrderPresenter();

        return $this->responseFactory->success([
            ...new ClientPresenter()->present($client),
            'orders' => array_map(
                $orderPresenter->present(...),
                $this->orders->find(new OrderFilter(clientId: $client->id)),
            ),
        ]);
    }
}
