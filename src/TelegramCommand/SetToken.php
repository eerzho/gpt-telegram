<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class SetToken extends BotCommandCustom
{
    protected $command = 'settoken';

    protected $description = 'Set your token';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), $this->getCommand());

        return 'Send your token or use the /cancel command to cancel';
    }
}