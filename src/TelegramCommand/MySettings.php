<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class MySettings extends BotCommandCustom
{
    protected $command = 'mysettings';

    protected $description = 'List of your settings';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        $resultText = $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);

        return true;
    }
}