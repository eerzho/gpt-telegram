<?php

namespace App\Command;

use App\Service\TelegramApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:telegram:set-commands',
    description: 'Sets commands for telegram bot',
    aliases: ['t:commands']
)]
class TelegramSetCommands extends Command
{
    public function __construct(private readonly TelegramApiService $telegramApiService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->telegramApiService->setCommands();
        $io->success('Commands set success!');

        return Command::SUCCESS;
    }
}
