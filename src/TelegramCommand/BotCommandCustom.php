<?php

namespace App\TelegramCommand;

use App\Constant\TelegramCommandRegistry;
use App\Entity\Chat;
use App\Service\CommandContainerService;
use JsonSerializable;
use TelegramBot\Api\Types\BotCommand;
use TelegramBot\Api\Types\Message;

abstract class BotCommandCustom extends BotCommand implements JsonSerializable
{
    public function __construct(protected readonly ?CommandContainerService $commandContainerService = null)
    {
    }

    abstract public function process(Message $message): string;

    public function postProcess(Chat $chat, Message $message): string
    {
        return '';
    }

    protected function getCommands(): array
    {
        return TelegramCommandRegistry::getActiveCommands();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'command' => $this->command,
            'description' => $this->description,
        ];
    }
}