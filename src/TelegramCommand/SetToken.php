<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class SetToken extends BotCommandCustom
{
    protected $command = 'settoken';

    protected $description = 'Set your token';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        $resultText = 'Send your token or use the /cancel command to cancel';

        return $this->commandContainerService->getCommandTService()->startCommand($chatT->getCommandT(), self::class);
    }

    public function postProcess(ChatT $chatT, Message $message, string &$resultText = ''): bool
    {
        $isSave = $this->commandContainerService->getChatTService()
            ->save($chatT->setChatGptApiToken($message->getText()));

        $isSave = $isSave && $this->commandContainerService->getCommandTService()
                ->stopCommand($chatT->getCommandT());

        $resultText = $this->commandContainerService->getChatTService()->getChatSettingsForTelegram($chatT);

        return $isSave;
    }
}