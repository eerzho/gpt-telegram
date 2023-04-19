<?php

namespace App\TelegramCommand;

use App\Entity\Chat;
use TelegramBot\Api\Types\Message;

class SetMaxTokens extends BotCommandCustom
{
    protected $command = 'setmaxtokens';

    protected $description = 'Set your max_tokens';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());
        $this->commandContainerService->getCommandService()->startCommand($chat->getCommand(), self::class);

        return 'Send your max_tokens or use the /cancel command to cancel';
    }

    public function postProcess(Chat $chat, Message $message): string
    {
        $maxTokens = $message->getText();
        if (!is_numeric($maxTokens)) {
            return "Incorrect value!\nSend the correct value or use the /cancel command to cancel";
        }

        $this->commandContainerService->getChatService()->saveMaxTokens($chat->getTelegramId(), intval($maxTokens));
        $this->commandContainerService->getCommandService()->stopCommand($chat->getCommand());

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}