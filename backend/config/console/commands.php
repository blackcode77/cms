<?php

declare(strict_types=1);

use App\Presentation\Console;

return [
    'app:hello' => Console\Hello\Action::class,
    'app:seed' => Console\Seed\Action::class,
];
