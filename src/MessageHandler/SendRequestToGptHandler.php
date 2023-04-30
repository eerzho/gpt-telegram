<?php

namespace App\MessageHandler;

use App\Message\SendRequestToGpt;
use App\Service\TelegramApiService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendRequestToGptHandler
{
    public function __construct(private TelegramApiService $telegramApiService)
    {
    }

    public function __invoke(SendRequestToGpt $message): void
    {
        $this->telegramApiService->sendMessageToCpt(
            $message->getChatT(),
            $message->getTUserMessage(),
            $message->getTWaitMessage()
        );
    }
}
