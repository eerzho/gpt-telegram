<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Repository\ChatTRepository;

readonly class ChatTService
{
    public function __construct(private ChatTRepository $chatTRepository)
    {
    }

    public function save(ChatT $chatT): bool
    {
        return $this->chatTRepository->save($chatT);
    }

    public function getChatByTelegramId(int $telegramId): ChatT
    {
        $chat = $this->chatTRepository->findByTelegramId($telegramId);

        if (!$chat) {
            $chat = (new ChatT())->setTelegramId($telegramId);
            $this->save($chat);
        }

        return $chat;
    }

    public function getChatSettingsForTelegram(ChatT $chatT): string
    {
        return sprintf(
            "Your settings:\n\tchat id - %d\n\ttoken - %s\n\tmodel - %s",
            $chatT->getTelegramId(),
            $chatT->getChatGptApiToken() ?? 'DEFAULT',
            $chatT->getChatGptModel()
        );
    }
}