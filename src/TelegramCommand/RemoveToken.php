<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class RemoveToken extends BotCommandCustom
{
    protected $command = 'removetoken';

    protected $description = 'Set default token';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveToken($message->getChat()->getId(), null);

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}