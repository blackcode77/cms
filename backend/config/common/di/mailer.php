<?php

declare(strict_types=1);

use App\Application\Shared\MailerInterface;
use App\Infrastructure\SymfonyMailer;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

// The test environment overrides this binding (config/environments/test/di.php).
return [
    MailerInterface::class => static fn() => new SymfonyMailer(
        new Mailer(Transport::fromDsn(getenv('MAILER_DSN') ?: 'null://null')),
        getenv('MAIL_FROM') ?: 'cms@localhost',
    ),
];
