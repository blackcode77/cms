<?php

declare(strict_types=1);

namespace App\Application\Client;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

/**
 * The phone identifies the Client (ADR 0006): a known phone gives the existing Client, completed with the details
 * it did not know yet; an unknown phone creates a new Client.
 */
final readonly class ClientResolver
{
    public function __construct(
        private ClientRepositoryInterface $clients,
    ) {}

    public function resolve(ClientPhone $phone, ClientDetails $details, DateTimeImmutable $now): ClientId
    {
        $client = $this->clients->findByPhone($phone);
        if ($client !== null) {
            if ($client->fillMissing($details)) {
                $this->clients->update($client);
            }

            return $client->id;
        }

        $client = new Client(new ClientId(Uuid::uuid7()), $phone, $details, '', $now);
        $this->clients->add($client);

        return $client->id;
    }
}
