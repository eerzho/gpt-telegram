<?php

namespace App\Command;

use App\Service\TelegramApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:telegram:webhook',
    description: 'Set or delete your webhook',
    aliases: ['t:webhook']
)]
class TelegramSetWebhookCommand extends Command
{
    public function __construct(private readonly TelegramApiService $telegramApiService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::OPTIONAL, 'Url to be used for webhook')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'Needed to delete the webhook');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('delete')) {
            $this->telegramApiService->deleteWebhook();
            $io->success('Webhook delete success!');

            return Command::SUCCESS;
        }

        $url = $input->getArgument('url');
        if (!$url) {
            $url = $io->askQuestion(new Question('Enter url address'));
        }
        $url = sprintf('%stelegram/handle', preg_replace('#([^/])$#', '$1/', $url));

        $this->telegramApiService->setWebhook($url);
        $io->success('Webhook set success!');

        return Command::SUCCESS;
    }
}
