<?php

namespace App\TelegramCommand;

use App\Entity\Chat;
use TelegramBot\Api\Types\Message;

class SetToken extends BotCommandCustom
{
    protected $command = 'settoken';

    protected $description = 'Set your token';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), self::class);

        return 'Send your token or use the /cancel command to cancel';
    }

    public function postProcess(Chat $chat, Message $message): string
    {
        $this->commandContainerService->getChatService()->saveToken($chat->getTelegramId(), $message->getText());
        $this->commandContainerService->getCommandService()->stopCommand($chat->getCommand());

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}