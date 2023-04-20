<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class SetModel extends BotCommandCustom
{
    protected $command = 'setmodel';

    protected $description = 'Set your model';

    public function process(ChatT $chatT, Message $message): string
    {
        $this->commandContainerService->getCommandTService()->startCommand($chatT->getCommandT(), self::class);

        return 'Send your model or use the /cancel command to cancel';
    }

    public function postProcess(ChatT $chatT, Message $message): string
    {
        $this->commandContainerService->getChatTService()->save($chatT->setChatGptModel($message->getText()));
        $this->commandContainerService->getCommandTService()->stopCommand($chatT->getCommandT());

        return $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);
    }
}