<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class RemoveToken extends BotCommandCustom
{
    protected $command = 'removetoken';

    protected $description = 'Set default token';

    public function process(ChatT $chatT, Message $message): string
    {
        $this->commandContainerService->getChatTService()->save($chatT->setChatGptApiToken(null));

        return $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);
    }
}