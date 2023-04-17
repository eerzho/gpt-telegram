<?php

namespace App\Service;

use App\Entity\Chat;
use App\Entity\Command;
use App\Repository\ChatRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository,
        private ParameterBagInterface $parameterBag
    ) {
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
        return $this->chatRepository->findByTelegramId($telegramId) ??
            (new Chat())
                ->setTelegramId($telegramId)
                ->setCommand(new Command());
    }

    public function getChatSettingsForTelegram(Chat $chat): string
    {
        return sprintf(
            "Your settings:\n\tchat id - %d\n\ttoken - %s\n\tmodel - %s",
            $chat->getTelegramId(),
            $chat->getChatGptApiToken() ?? 'API_TOKEN (default)',
            $chat->getChatGptModel() ?? sprintf('%s (default)', $this->parameterBag->get('app.api.chat_gpt.model')),
        );
    }
}