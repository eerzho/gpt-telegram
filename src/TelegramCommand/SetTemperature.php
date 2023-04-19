<?php

namespace App\TelegramCommand;

use App\Entity\Chat;
use TelegramBot\Api\Types\Message;

class SetTemperature extends BotCommandCustom
{
    protected $command = 'settemperature';

    protected $description = 'Set your temperature';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), self::class);

        return 'Send your temperature or use the /cancel command to cancel';
    }

    public function postProcess(Chat $chat, Message $message): string
    {
        $temperature = $message->getText();
        if (!is_numeric($temperature)) {
            return "Incorrect value!\nSend the correct value or use the /cancel command to cancel";
        }

        $this->commandContainerService->getChatService()->saveTemperature($chat->getTelegramId(), intval($temperature));
        $this->commandContainerService->getCommandService()->stopCommand($chat->getCommand());

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}