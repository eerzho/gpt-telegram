<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class Cancel extends BotCommandCustom
{
    protected $command = 'cancel';

    protected $description = 'Cancel active command or to end a chat with Gpt';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        if ($chatT->getCommandT()->isActive()) {
            $isSave = $this->commandContainerService->getCommandTService()->stopCommand($chatT->getCommandT());
            $resultText = 'Command canceled';
        } else {
            $isSave = $this->commandContainerService->getMessageTService()->removeAllByChat($chatT);
            $resultText = 'Chat cleared';
        }

        return $isSave;
    }
}