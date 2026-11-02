<?php

declare(strict_types=1);

namespace App\Presentation\Console\Seed;

use App\Application\Seed\SeedDefaults\Command as SeedCommand;
use App\Application\Seed\SeedDefaults\Handler;
use App\Domain\User\Value\UserEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Yiisoft\Yii\Console\ExitCode;

#[AsCommand(
    name: 'app:seed',
    description: 'Idempotently create default Roles and the Initial admin from ADMIN_EMAIL / ADMIN_PASSWORD',
)]
final class Action extends Command
{
    public function __construct(
        private readonly Handler $handler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = getenv('ADMIN_EMAIL');
        $password = getenv('ADMIN_PASSWORD');
        if ($email === false || $email === '' || $password === false || $password === '') {
            $output->writeln('<error>ADMIN_EMAIL and ADMIN_PASSWORD must be set.</error>');

            return ExitCode::CONFIG;
        }

        try {
            $this->handler->handle(new SeedCommand(new UserEmail($email), $password));
        } catch (Throwable $e) {
            $output->writeln('<error>Seeding failed: ' . $e->getMessage() . '</error>');

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $output->writeln('Seeded default roles and the initial admin.');

        return ExitCode::OK;
    }
}
