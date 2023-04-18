<?php

namespace App\TelegramCommand;

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

    protected function getCommands(): array
    {
        return [
            Start::class,
            Help::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            SetTemperature::class,
            RemoveTemperature::class,
            SetMaxTokens::class,
            RemoveMaxTokens::class,
            Cancel::class,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'command' => $this->command,
            'description' => $this->description,
        ];
    }
}