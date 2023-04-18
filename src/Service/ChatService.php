<?php

namespace App\Service;

use App\Entity\Chat;
use App\Entity\Command;
use App\Repository\ChatRepository;

readonly class ChatService
{
    public function __construct(private ChatRepository $chatRepository)
    {
    }

    public function saveId(int $telegramId): Chat
    {
        $chat = $this->getChat($telegramId);
        $this->chatRepository->save($chat, true);

        return $chat;
    }

    public function saveToken(int $telegramId, ?string $token): Chat
    {
        $chat = $this->getChat($telegramId);
        $chat->setChatGptApiToken($token);
        $this->chatRepository->save($chat, true);

        return $chat;
    }

    public function saveModel(int $telegramId, ?string $model): Chat
    {
        $chat = $this->getChat($telegramId);
        $chat->setChatGptModel($model);
        $this->chatRepository->save($chat, true);

        return $chat;
    }

    public function saveTemperature(int $telegramId, ?int $temperature): Chat
    {
        $chat = $this->getChat($telegramId);
        $chat->setTemperature($temperature);
        $this->chatRepository->save($chat, true);

        return $chat;
    }

    public function saveMaxTokens(int $telegramId, ?int $maxTokens): Chat
    {
        $chat = $this->getChat($telegramId);
        $chat->setMaxTokens($maxTokens);
        $this->chatRepository->save($chat, true);

        return $chat;
    }

    private function getChat(int $telegramId): Chat
    {
        return $this->chatRepository->findByTelegramId($telegramId) ??
            (new Chat())
                ->setTelegramId($telegramId)
                ->setCommand(new Command());
    }

    public function getChatSettingsForTelegram(Chat $chat): string
    {
        return sprintf(
            "Your settings:\n\tchat id - %d\n\ttoken - %s\n\tmodel - %s\n\ttemperature - %d\n\tmax_tokens - %d",
            $chat->getTelegramId(),
            $chat->getChatGptApiToken() ?? 'DEFAULT',
            $chat->getChatGptModel(),
            $chat->getTemperature(),
            $chat->getMaxTokens()
        );
    }
}