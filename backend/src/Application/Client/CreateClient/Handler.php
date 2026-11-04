<?php

declare(strict_types=1);

namespace App\Application\Client\CreateClient;

use App\Application\Client\Exception\ClientPhoneAlreadyExistsException;
use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Client\Value\ClientId;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Ramsey\Uuid\Uuid;

final readonly class Handler
{
    public function __construct(
        private ClientRepositoryInterface $clients,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws ClientPhoneAlreadyExistsException
     */
    public function handle(Command $command): ClientId
    {
        if ($this->clients->findByPhone($command->phone) !== null) {
            throw ClientPhoneAlreadyExistsException::create();
        }

        $id = new ClientId(Uuid::uuid7());
        $this->clients->add(
            new Client(
                $id,
                $command->phone,
                $command->details,
                $command->comment,
                DateTimeImmutable::createFromInterface($this->clock->now()),
            ),
        );

        return $id;
    }
}
