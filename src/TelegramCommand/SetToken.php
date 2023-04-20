<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class SetToken extends BotCommandCustom
{
    protected $command = 'settoken';

    protected $description = 'Set your token';

    public function process(ChatT $chatT, Message $message): string
    {
        $this->commandContainerService->getCommandTService()->startCommand($chatT->getCommandT(), self::class);

        return 'Send your token or use the /cancel command to cancel';
    }

    public function postProcess(ChatT $chatT, Message $message): string
    {
        $this->commandContainerService->getChatTService()->save($chatT->setChatGptApiToken($message->getText()));
        $this->commandContainerService->getCommandTService()->stopCommand($chatT->getCommandT());

        return $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);
    }
}