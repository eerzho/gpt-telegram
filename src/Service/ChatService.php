<?php

namespace App\Service;

use App\Entity\Chat;
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

    private function getChat(int $telegramId): Chat
    {
        return $this->chatRepository->findByTelegramId($telegramId) ?? (new Chat())->setTelegramId($telegramId);
    }
}