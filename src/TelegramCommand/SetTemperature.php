<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class SetTemperature extends BotCommandCustom
{
    protected $command = 'settemperature';

    protected $description = 'Set your temperature';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), $this->getCommand());

        return 'Send your temperature or use the /cancel command to cancel';
    }
}