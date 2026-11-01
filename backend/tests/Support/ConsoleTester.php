<?php

declare(strict_types=1);

namespace App\Tests\Support;

use function dirname;

class ConsoleTester extends \Codeception\Actor
{
    use _generated\ConsoleTesterActions;

    public function runApp(?string $parameters = null): void
    {
        $this->runShellCommand(
            dirname(__DIR__, 2) . '/yii' . ($parameters === null ? '' : (' ' . $parameters)),
        );
    }
}
