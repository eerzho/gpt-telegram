<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class RemoveTemperature extends BotCommandCustom
{
    protected $command = 'removetemperature';

    protected $description = 'Set default temperature';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveTemperature($message->getChat()->getId(), null);

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}