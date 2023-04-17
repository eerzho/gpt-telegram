<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class SetModel extends BotCommandCustom
{
    protected $command = 'setmodel';

    protected $description = 'Set your model';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), $this->getCommand());

        return 'Send your model or use the /cancel command to cancel';
    }
}