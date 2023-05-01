<?php

namespace App\TelegramCommand;

use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use App\Service\ChatTService;
use TelegramBot\Api\Types\Message;

readonly class RemoveModel implements CommandProcessInterface
{
    public function __construct(private ChatTService $chatTService)
    {
    }

    public function getCommand(): string
    {
        return 'removemodel';
    }

    public function getTextValue(): ?string
    {
        return null;
    }

    public function getDescription(): string
    {
        return 'Set default model';
    }

    public function process(Message $message): CommandResult
    {
        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        return new CommandResult(
            $this->chatTService->save($chatT->setChatGptModel(null)),
            $this->chatTService->getChatSettingsForTelegram($chatT)
        );
    }
}