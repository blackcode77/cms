<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Application\Shared\MailerInterface;
use Symfony\Component\Mailer\Exception\ExceptionInterface as MailerException;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\ExceptionInterface as MimeException;

final readonly class SymfonyMailer implements MailerInterface
{
    public function __construct(
        private SymfonyMailerInterface $mailer,
        private string $from,
    ) {}

    public function send(Mail $mail): void
    {
        try {
            $email = new Email()
                ->from($this->from)
                ->to($mail->to)
                ->subject($mail->subject)
                ->text($mail->body);
            $this->mailer->send($email);
        } catch (MailerException | MimeException $e) {
            // transport failures and malformed addresses alike: the caller only needs to know the mail was not sent
            throw new MailDeliveryException('Could not send mail: ' . $e->getMessage(), previous: $e);
        }
    }
}
