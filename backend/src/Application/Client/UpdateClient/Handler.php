<?php

declare(strict_types=1);

namespace App\Application\Client\UpdateClient;

use App\Application\Client\Exception\ClientNotFoundException;
use App\Application\Client\Exception\ClientPhoneAlreadyExistsException;
use App\Domain\Client\Repository\ClientRepositoryInterface;

final readonly class Handler
{
    public function __construct(
        private ClientRepositoryInterface $clients,
    ) {}

    /**
     * Clients are never merged: a phone already used by another Client is rejected.
     *
     * @throws ClientNotFoundException
     * @throws ClientPhoneAlreadyExistsException
     */
    public function handle(Command $command): void
    {
        $client = $this->clients->findById($command->id) ?? throw ClientNotFoundException::create();

        $other = $this->clients->findByPhone($command->phone);
        if ($other !== null && !$other->id->isEqualTo($client->id)) {
            throw ClientPhoneAlreadyExistsException::create();
        }

        $client->edit($command->phone, $command->details, $command->comment);
        $this->clients->update($client);
    }
}
