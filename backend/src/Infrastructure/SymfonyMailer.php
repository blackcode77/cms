<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Application\Shared\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;

final readonly class SymfonyMailer implements MailerInterface
{
    public function __construct(
        private SymfonyMailerInterface $mailer,
        private string $from,
    ) {}

    public function send(Mail $mail): void
    {
        $email = new Email()
            ->from($this->from)
            ->to($mail->to)
            ->subject($mail->subject)
            ->text($mail->body);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new MailDeliveryException('Could not send mail: ' . $e->getMessage(), previous: $e);
        }
    }
}
