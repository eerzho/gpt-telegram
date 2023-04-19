<?php

namespace App\Command;

use App\Service\TelegramService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:telegram:set-webhook',
    description: 'Sets webhook for telegram bot',
    aliases: ['t:webhook']
)]
class TelegramSetWebhookCommand extends Command
{
    public function __construct(private readonly TelegramService $telegramService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::OPTIONAL, 'Url to be used for webhook');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument('url');

        if (!$url) {
            $io->error("You didn't send url");

            return Command::FAILURE;
        }

        $url = sprintf('%stelegram/handle', preg_replace('#([^/])$#', '$1/', $url));
        $this->telegramService->setWebhook($url);

        $io->success('Webhook set success!');

        return Command::SUCCESS;
    }
}
