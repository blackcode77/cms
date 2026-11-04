<?php

declare(strict_types=1);

namespace App\Domain\Client\Entity;

use App\Domain\Client\Value\ClientDetails;
use App\Domain\Client\Value\ClientId;
use App\Domain\Client\Value\ClientPhone;
use DateTimeImmutable;

final class Client
{
    public readonly ClientId $id;
    public private(set) ClientPhone $phone;
    public private(set) ClientDetails $details;
    public private(set) string $comment;
    public readonly DateTimeImmutable $createdAt;

    public function __construct(
        ClientId $id,
        ClientPhone $phone,
        ClientDetails $details,
        string $comment,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->phone = $phone;
        $this->details = $details;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
    }

    public function edit(ClientPhone $phone, ClientDetails $details, string $comment): void
    {
        $this->phone = $phone;
        $this->details = $details;
        $this->comment = $comment;
    }

    /**
     * Fills only what is not yet known: details typed by a visitor never overwrite those already stored.
     *
     * @return bool whether anything changed
     */
    public function fillMissing(ClientDetails $details): bool
    {
        $completed = $this->details->completedWith($details);
        if ($completed == $this->details) {
            return false;
        }
        $this->details = $completed;

        return true;
    }
}
