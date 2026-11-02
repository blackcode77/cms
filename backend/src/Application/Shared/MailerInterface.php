<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface MailerInterface
{
    /**
     * Sends synchronously (no queue).
     *
     * @throws MailDeliveryException when the mail could not be handed to the mail server
     */
    public function send(Mail $mail): void;
}
