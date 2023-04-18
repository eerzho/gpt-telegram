<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class RemoveMaxTokens extends BotCommandCustom
{
    protected $command = 'removemaxtokens';

    protected $description = 'Set default max_tokens';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveMaxTokens($message->getChat()->getId(), null);

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}