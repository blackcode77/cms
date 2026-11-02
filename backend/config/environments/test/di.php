<?php

declare(strict_types=1);

use App\Application\Shared\MailerInterface;
use App\Tests\Support\InMemoryMailer;
use App\Tests\Support\ShiftableClock;
use Psr\Clock\ClockInterface;

// Test doubles live in tests/Support and are bound only for APP_ENV=test.
return [
    ClockInterface::class => ShiftableClock::class,
    MailerInterface::class => InMemoryMailer::class,
];
