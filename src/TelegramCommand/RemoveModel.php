<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class RemoveModel extends BotCommandCustom
{
    protected $command = 'removemodel';

    protected $description = 'Set default model';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveModel($message->getChat()->getId(), null);

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}