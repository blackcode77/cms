<?php

declare(strict_types=1);

namespace App\Domain\Client\Repository;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;

interface ClientRepositoryInterface
{
    public function findById(ClientId $id): ?Client;

    public function findByPhone(ClientPhone $phone): ?Client;

    public function add(Client $client): void;

    public function update(Client $client): void;

    /**
     * Removes the Client together with all their Orders.
     */
    public function remove(ClientId $id): void;
}
