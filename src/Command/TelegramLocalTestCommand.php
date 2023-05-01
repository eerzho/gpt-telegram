<?php

namespace App\Command;

use App\Service\TelegramApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:telegram:local-test',
    description: 'Local messages test',
    aliases: ['t:test']
)]
class TelegramLocalTestCommand extends Command
{
    public function __construct(private readonly TelegramApiService $telegramApiService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->telegramApiService->getUpdates();

        $io->success('Success!');

        return Command::SUCCESS;
    }
}
