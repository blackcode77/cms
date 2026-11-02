<?php

declare(strict_types=1);

use App\Infrastructure\SystemClock;
use Psr\Clock\ClockInterface;

// The test environment overrides this binding (config/environments/test/di.php).
return [
    ClockInterface::class => SystemClock::class,
];
