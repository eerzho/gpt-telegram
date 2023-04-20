<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\CommandT;
use App\Repository\ChatTRepository;

readonly class ChatTService
{
    public function __construct(private ChatTRepository $chatTRepository, private EncryptionService $encryptionService)
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
            "Your settings:\n\tchat id - %d\n\ttoken - %s\n\tmodel - %s",
            $chatT->getTelegramId(),
            $chatT->getChatGptApiToken() ? $this->encryptionService->decrypt($chatT->getChatGptApiToken()) : 'DEFAULT',
            $chatT->getChatGptModel()
        );
    }
}