<?php

declare(strict_types=1);

namespace App\Presentation\Api\Client\ByPhone;

use App\Domain\Client\Value\ClientPhone;
use App\Presentation\Api\Client\Shared\ClientPresenter;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use App\Shared\Read\Client\ClientReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * `GET /clients/by-phone?phone=...`: the Client a new Order with this phone would go to, or `null`.
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
        $raw = $query['phone'] ?? '';
        $phone = is_string($raw) ? ClientPhone::tryFrom($raw) : null;
        $client = $phone === null ? null : $this->reader->getByPhone($phone);

        return $this->responseFactory->success($client === null ? null : new ClientPresenter()->present($client));
    }
}
