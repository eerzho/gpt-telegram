<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class SetMaxTokens extends BotCommandCustom
{
    protected $command = 'setmaxtokens';

    protected $description = 'Set your max_tokens';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), $this->getCommand());

        return 'Send your max_tokens or use the /cancel command to cancel';
    }
}