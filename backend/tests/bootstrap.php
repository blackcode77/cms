<?php

declare(strict_types=1);

App\Environment::prepare();

// Tests never depend on the developer's .env: the app refuses short or placeholder secrets.
putenv('JWT_SECRET=test-only-secret-0123456789abcdef0123456789abcdef');
