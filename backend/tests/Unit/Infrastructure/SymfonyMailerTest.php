<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure;

use App\Application\Shared\Mail;
use App\Application\Shared\MailDeliveryException;
use App\Infrastructure\SymfonyMailer;
use Codeception\Test\Unit;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\LogicException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\RawMessage;

final class SymfonyMailerTest extends Unit
{
    public function testInvalidRecipientAddressBecomesMailDeliveryException(): void
    {
        $mailer = new SymfonyMailer(new Mailer(Transport::fromDsn('null://null')), 'cms@localhost');

        $this->expectException(MailDeliveryException::class);
        $mailer->send(new Mail('not an address', 'Subject', 'Body'));
    }

    public function testInvalidSenderAddressBecomesMailDeliveryException(): void
    {
        $mailer = new SymfonyMailer(new Mailer(Transport::fromDsn('null://null')), 'broken from');

        $this->expectException(MailDeliveryException::class);
        $mailer->send(new Mail('user@example.com', 'Subject', 'Body'));
    }

    public function testOtherMailerLibraryExceptionsAreMapped(): void
    {
        $failing = new class implements MailerInterface {
            public function send(RawMessage $message, ?Envelope $envelope = null): void
            {
                throw new LogicException('boom');
            }
        };

        $this->expectException(MailDeliveryException::class);
        new SymfonyMailer($failing, 'cms@localhost')->send(new Mail('user@example.com', 'S', 'B'));
    }

    public function testValidMailPasses(): void
    {
        new SymfonyMailer(new Mailer(Transport::fromDsn('null://null')), 'cms@localhost')
            ->send(new Mail('user@example.com', 'S', 'B'));

        $this->assertTrue(true);
    }
}
