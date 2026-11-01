<?php

declare(strict_types=1);

namespace App\Presentation\Api\Health;

use App\Presentation\Api\Shared\ResponseFactory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private ConnectionInterface $db,
    ) {}

    public function __invoke(): ResponseInterface
    {
        $this->db->createCommand('SELECT 1')->queryScalar();

        return $this->responseFactory->success(['app' => 'ok', 'database' => 'ok']);
    }
}
