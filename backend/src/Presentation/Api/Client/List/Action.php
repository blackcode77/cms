<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\List;

use App\Presentation\Api\Client\Shared\ClientPresenter;
use App\Presentation\Api\Shared\ResponseFactory\Presenter\CollectionPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Client\ClientReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * `GET /clients?q=<phone digits, name or email>`, newest first.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ClientReader $reader,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if ($query === []) {
            parse_str($request->getUri()->getQuery(), $query);
        }
        $search = $query['q'] ?? '';

        return $this->responseFactory->success(
            $this->reader->find(is_string($search) ? mb_substr($search, 0, 100) : ''),
            new CollectionPresenter(new ClientPresenter()),
        );
    }
}
