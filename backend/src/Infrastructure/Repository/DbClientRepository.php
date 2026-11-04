<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Client\Entity\Client;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientEmail;
use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;
use App\Infrastructure\DataMapper\EntityHydratorInterface;
use App\Infrastructure\Table;
use DateTimeImmutable;
use Yiisoft\Db\Connection\ConnectionInterface;

final readonly class DbClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private EntityHydratorInterface $entityHydrator,
        private ConnectionInterface $db,
    ) {}

    public function findById(ClientId $id): ?Client
    {
        return $this->findOneBy(['id' => $id->toString()]);
    }

    public function findByPhone(ClientPhone $phone): ?Client
    {
        return $this->findOneBy(['phone' => $phone->toString()]);
    }

    public function add(Client $client): void
    {
        $this->db->createCommand()
            ->insert(Table::CLIENTS, [
                'id' => $client->id->toString(),
                ...self::columns($client),
                'created_at' => $client->createdAt->format('Y-m-d H:i:s.uP'),
            ])
            ->execute();
    }

    public function update(Client $client): void
    {
        $this->db->createCommand()
            ->update(Table::CLIENTS, self::columns($client), ['id' => $client->id->toString()])
            ->execute();
    }

    public function remove(ClientId $id): void
    {
        // Orders go with the Client (ON DELETE CASCADE)
        $this->db->createCommand()
            ->delete(Table::CLIENTS, ['id' => $id->toString()])
            ->execute();
    }

    /**
     * @return array<string, string|null>
     */
    private static function columns(Client $client): array
    {
        return [
            'phone' => $client->phone->toString(),
            'first_name' => $client->details->firstName,
            'last_name' => $client->details->lastName,
            'middle_name' => $client->details->middleName,
            'email' => $client->details->email?->toString(),
            'comment' => $client->comment,
        ];
    }

    /**
     * @param array<string, string> $condition
     */
    private function findOneBy(array $condition): ?Client
    {
        /**
         * @var array{
         *     id: string, phone: string, first_name: string, last_name: string, middle_name: string,
         *     email: string|null, comment: string, created_at: string,
         * }|null $row
         */
        $row = $this->db->createQuery()
            ->from(Table::CLIENTS)
            ->where($condition)
            ->one();
        if ($row === null) {
            return null;
        }

        return $this->entityHydrator->create(
            Client::class,
            [
                'id' => ClientId::fromString($row['id']),
                'phone' => new ClientPhone($row['phone']),
                'details' => new ClientDetails(
                    $row['first_name'],
                    $row['last_name'],
                    $row['middle_name'],
                    $row['email'] === null ? null : new ClientEmail($row['email']),
                ),
                'comment' => $row['comment'],
                'createdAt' => new DateTimeImmutable($row['created_at']),
            ],
        );
    }
}
