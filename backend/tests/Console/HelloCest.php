<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Tests\Support\ConsoleTester;

final class HelloCest
{
    public function listsCommands(ConsoleTester $I): void
    {
        $I->runApp();
        $I->canSeeResultCodeIs(0);
        $I->seeInShellOutput('app:hello');
    }

    public function helloCommandWorks(ConsoleTester $I): void
    {
        $I->runApp('app:hello');
        $I->canSeeResultCodeIs(0);
        $I->seeInShellOutput('Hello from CMS console');
    }
}
