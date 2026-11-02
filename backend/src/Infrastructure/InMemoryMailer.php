<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Application\Shared\MailerInterface;

/**
 * Mailer bound in the test environment: records mails instead of sending them and can simulate a send failure.
 * State is static because the application is rebuilt for every in-process request.
 */
final class InMemoryMailer implements MailerInterface
{
    /** @var list<array{to: string, subject: string, body: string}> */
    private static array $sent = [];
    private static bool $failing = false;

    public static function reset(): void
    {
        self::$sent = [];
        self::$failing = false;
    }

    public static function failSending(): void
    {
        self::$failing = true;
    }

    /**
     * @return list<array{to: string, subject: string, body: string}>
     */
    public static function sent(): array
    {
        return self::$sent;
    }

    public function send(Mail $mail): void
    {
        if (self::$failing) {
            throw new MailDeliveryException('Simulated mail failure.');
        }

        self::$sent[] = ['to' => $mail->to, 'subject' => $mail->subject, 'body' => $mail->body];
    }
}
