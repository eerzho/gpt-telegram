<?php

namespace App\TelegramCommand;

use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use App\Service\ChatTService;
use TelegramBot\Api\Types\Message;

readonly class Settings implements CommandProcessInterface
{
    public function __construct(private ChatTService $chatTService)
    {
    }

    public function getCommand(): string
    {
        return 'settings';
    }

    public function getTextValue(): ?string
    {
        return "🛠️ Settings";
    }

    public function getDescription(): string
    {
        return 'List of your settings';
    }

    public function process(Message $message): CommandResult
    {
        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        return new CommandResult(
            true,
            $this->chatTService->getChatSettingsForTelegram($chatT)
        );
    }
}