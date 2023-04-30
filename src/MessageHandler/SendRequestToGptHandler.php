<?php

namespace App\MessageHandler;

use App\Message\SendRequestToGpt;
use App\Service\ChatTService;
use App\Service\TelegramApiService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendRequestToGptHandler
{
    public function __construct(private TelegramApiService $telegramApiService, private ChatTService $chatTService)
    {
    }

    public function __invoke(SendRequestToGpt $message): void
    {
        $this->telegramApiService->sendMessageToCpt(
            $this->chatTService->getChatByTelegramId($message->getTUserMessage()->getChat()->getId()),
            $message->getTUserMessage(),
            $message->getTWaitMessage()
        );
    }
}
