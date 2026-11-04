<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Managers;

use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Order\ManagerOption;
use App\Shared\Read\Order\OrderReader;
use Psr\Http\Message\ResponseInterface;

/**
 * Users who can be assigned as the Manager of an Order.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private OrderReader $reader,
    ) {}

    public function __invoke(): ResponseInterface
    {
        return $this->responseFactory->success(
            array_map(
                static fn(ManagerOption $m): array => ['id' => $m->id->toString(), 'name' => $m->name, 'email' => $m->email],
                $this->reader->managers(),
            ),
        );
    }
}
