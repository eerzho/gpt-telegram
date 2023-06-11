<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\CommandT;
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
            $chat = (new ChatT())
                ->setTelegramId($telegramId)
                ->setCommandT(new CommandT());
            $this->chatTRepository->save($chat, true);
        }

        return $chat;
    }

    public function getChatSettingsForTelegram(ChatT $chatT): string
    {
        return sprintf(
            "Your settings:\nChat ID - %d \nComing soon...",
            $chatT->getTelegramId(),
        );
    }

    /**
     * @return ChatT[]
     */
    public function getAll(): array
    {
        return $this->chatTRepository->getAll();
    }
}