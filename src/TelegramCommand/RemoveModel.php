<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class RemoveModel extends BotCommandCustom
{
    protected $command = 'removemodel';

    protected $description = 'Set default model';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        $resultText = $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);

        return $this->commandContainerService->getChatTService()->save($chatT->setChatGptModel(null));
    }
}