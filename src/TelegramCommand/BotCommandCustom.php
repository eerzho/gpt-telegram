<?php

namespace App\TelegramCommand;

use App\Constant\TelegramCommandRegistry;
use App\Entity\ChatT;
use App\Service\CommandContainerService;
use JsonSerializable;
use TelegramBot\Api\Types\BotCommand;
use TelegramBot\Api\Types\Message;

abstract class BotCommandCustom extends BotCommand implements JsonSerializable
{
    public function __construct(protected readonly ?CommandContainerService $commandContainerService = null)
    {
    }

    abstract public function process(ChatT $chatT, Message $message, string &$resultText = ''): bool;

    public function postProcess(ChatT $chatT, Message $message, string &$resultText = ''): bool
    {
        return true;
    }

    protected function getCommands(): array
    {
        return TelegramCommandRegistry::getShowCommands();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'command' => $this->command,
            'description' => $this->description,
        ];
    }
}