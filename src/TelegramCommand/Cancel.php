<?php

namespace App\TelegramCommand;

use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use App\Service\ChatTService;
use App\Service\CommandTService;
use App\Service\MessageTService;
use TelegramBot\Api\Types\Message;

readonly class Cancel implements CommandProcessInterface
{
    public function __construct(
        private ChatTService $chatTService,
        private CommandTService $commandTService,
        private MessageTService $messageTService
    ) {
    }

    public function getCommand(): string
    {
        return 'cancel';
    }

    public function getTextValue(): ?string
    {
        return "🗑️";
    }

    public function getDescription(): string
    {
        return 'Cancel active command or start new chat with GPT';
    }

    public function process(Message $message): CommandResult
    {
        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        if ($chatT->getCommandT()->isActive()) {
            $isSave = $this->commandTService->stopCommand($chatT->getCommandT());
            $text = 'Command canceled';
        } else {
            $isSave = $this->messageTService->removeAllByChat($chatT);
            $text = 'Chat cleared and send a new message to start chat';
        }

        return new CommandResult($isSave, $text);
    }
}