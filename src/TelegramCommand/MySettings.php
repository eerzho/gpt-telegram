<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class MySettings extends BotCommandCustom
{
    protected $command = 'mysettings';

    protected $description = 'List of your settings';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}