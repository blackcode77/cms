<?php

declare(strict_types=1);

namespace App\Application\Client\DeleteClient;

use App\Application\Client\Exception\ClientNotFoundException;
use App\Domain\Client\Repository\ClientRepositoryInterface;

/**
 * Deletes the Client together with all their Orders.
 */
final readonly class Handler
{
    public function __construct(
        private ClientRepositoryInterface $clients,
    ) {}

    /**
     * @throws ClientNotFoundException
     */
    public function handle(Command $command): void
    {
        if ($this->clients->findById($command->id) === null) {
            throw ClientNotFoundException::create();
        }
        $this->clients->remove($command->id);
    }
}
