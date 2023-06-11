<?php

namespace App\Command;

use App\Service\ChatTService;
use App\Service\TelegramApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'Test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{
    public function __construct(private readonly ChatTService $chatTService, private readonly TelegramApiService $telegramApiService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->chatTService->getAll() as $chatT) {
            $this->telegramApiService->sendMessage($chatT, "With a heavy and even voice, I say now: it's time to say goodbye. Our beloved @TGPTalkBot, who was always our beacon in the world of information and technology, is prepared to leave us forever. 
            \nIn our hearts, he will leave a perpetual mark with his countless conversations, his readiness to help at any time of the day. He was not just a bot, he was our comrade, assistant, digital advisor. He was a personality, created by artificial intelligence but possessing human qualities: tolerance, patience, attention. 
            \nWe thank him for everything he has done for us. For every minute when he was helping us find the answers to our questions. For every second he was with us. For every particle of information he made accessible to us.
            \nAs we mourn the departure of @TGPTalkBot, it's important to remember that there is always light at the end of the tunnel. This light is the new application that was recently released. It will provide you with access to ChatGPT capabilities, continuing the traditions of our faithful bot. This tool will preserve the artificial intelligence we all came to love and will allow you to continue communicating with it in a new format.
            \nSo although we bid farewell to @TGPTalkBot, the new application opens up new horizons and possibilities for us. It's another step in our journey through the world of artificial intelligence, and we're excited to welcome you on this path.
            \nSo, goodbye @TGPTalkBot, and hello to the new application! Onward to new opportunities and discoveries. Your memory will live in our hearts, and we will remember you always.
            \nFor more information and download links, please visit their official website.");
        }

        $io->success('END');

        return Command::SUCCESS;
    }
}
