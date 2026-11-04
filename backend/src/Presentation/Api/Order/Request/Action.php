<?php

declare(strict_types=1);

namespace App\Presentation\Api\Order\Request;

use App\Application\Order\SubmitRequest\Command;
use App\Application\Order\SubmitRequest\Handler;
use App\Presentation\Api\Auth\Shared\ClientIp;
use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Public. An Order request from the site's request form.
 */
final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
    ) {}

    public function __invoke(Input $input, ServerRequestInterface $request): ResponseInterface
    {
        $this->handler->handle(
            new Command(
                $input->phone(),
                $input->clientDetails(),
                $input->cargo(),
                trim($input->message),
                ClientIp::fromRequest($request),
            ),
        );

        return $this->responseFactory->success()->withStatus(201);
    }
}
