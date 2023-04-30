<?php

namespace App\MessageHandler;

use App\Message\SendRequestToGpt;
use App\Service\TelegramService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendRequestToGptHandler
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function __invoke(SendRequestToGpt $message): void
    {
        $this->telegramService->sendMessageToCpt(
            $message->getChatT(),
            $message->getTUserMessage(),
            $message->getTWaitMessage()
        );
    }
}
