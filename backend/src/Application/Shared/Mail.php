<?php

declare(strict_types=1);

namespace App\Application\Shared;

final readonly class Mail
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $body,
    ) {}

    /**
     * Mail that delivers a generated password together with the login (the User's email).
     */
    public static function credentials(string $login, string $password, string $subject): self
    {
        return new self(
            $login,
            $subject,
            "Your account for the CMS admin area.\n\nLogin: {$login}\nPassword: {$password}\n\n"
            . "You will be asked to choose a new password on your first sign-in.\n",
        );
    }
}
