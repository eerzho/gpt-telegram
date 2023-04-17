<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class Cancel extends BotCommandCustom
{
    protected $command = 'cancel';

    protected $description = 'Cancel active command';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->stopCommand($chat->getCommand());

        return 'Command canceled';
    }
}