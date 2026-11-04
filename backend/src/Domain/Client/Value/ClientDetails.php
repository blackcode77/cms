<?php

declare(strict_types=1);

namespace App\Domain\Client\Value;

/**
 * What is known about a Client besides the phone. Every part is optional (empty string or null = unknown).
 */
final readonly class ClientDetails
{
    public const int NAME_LENGTH_LIMIT = 100;

    public function __construct(
        public string $firstName = '',
        public string $lastName = '',
        public string $middleName = '',
        public ?ClientEmail $email = null,
    ) {}

    /**
     * Known parts of this, completed with the parts of $other this does not know.
     */
    public function completedWith(self $other): self
    {
        return new self(
            $this->firstName !== '' ? $this->firstName : $other->firstName,
            $this->lastName !== '' ? $this->lastName : $other->lastName,
            $this->middleName !== '' ? $this->middleName : $other->middleName,
            $this->email ?? $other->email,
        );
    }
}
