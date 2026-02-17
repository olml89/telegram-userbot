<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Console;

use olml89\TelegramUserbot\Backend\Category\Domain\CategoryStorageException;
use olml89\TelegramUserbot\Backend\Shared\Application\SeedDatabaseCommand as ApplicationSeedDatabaseCommand;
use olml89\TelegramUserbot\Backend\Shared\Application\SeedDatabaseCommandHandler;
use olml89\TelegramUserbot\Backend\Shared\Domain\ValueObject\Name\NameLengthException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:db:seed',
    description: 'Seed database with initial data',
)]
final class SeedDatabaseCommand extends Command
{
    public function __construct(
        private readonly SeedDatabaseCommandHandler $seedDatabaseCommandHandler,
    ) {
        parent::__construct();
    }

    /**
     * @throws NameLengthException
     * @throws CategoryStorageException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $seedDatabaseCommand = new ApplicationSeedDatabaseCommand(
            categoryNames: [
                'Belly',
                'Feet',
                'Creampie',
            ],
        );

        foreach ($this->seedDatabaseCommandHandler->handle($seedDatabaseCommand) as $categoryResult) {
            $io->success(sprintf('Category seeded: %s', $categoryResult->name));
        }

        return Command::SUCCESS;
    }
}
