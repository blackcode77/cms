<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\List;

use App\Domain\Client\Value\ClientId;
use App\Domain\Order\Value\OrderStatus;
use App\Domain\User\Value\UserId;
use App\Presentation\Api\Auth\Shared\Authenticate;
use App\Presentation\Api\Order\Shared\OrderPresenter;
use App\Presentation\Api\Shared\ResponseFactory\Presenter\CollectionPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Order\OrderFilter;
use App\Shared\Read\Order\OrderReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * `GET /orders?status=request&manager=me|<user id>&client_id=<id>`, newest first.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private OrderReader $reader,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if ($query === []) {
            parse_str($request->getUri()->getQuery(), $query);
        }
        $status = $query['status'] ?? '';
        $manager = $query['manager'] ?? '';
        $client = $query['client_id'] ?? '';

        $filter = new OrderFilter(
            is_string($status) && $status !== '' ? OrderStatus::tryFrom($status) : null,
            $manager === 'me'
                ? Authenticate::currentUser($request)->user->id
                : UserId::tryFromString(is_string($manager) ? $manager : null),
            ClientId::tryFromString(is_string($client) ? $client : null),
        );

        return $this->responseFactory->success(
            $this->reader->find($filter),
            new CollectionPresenter(new OrderPresenter()),
        );
    }
}
