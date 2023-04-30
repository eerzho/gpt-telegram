<?php

namespace App\TelegramCommand;

use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use TelegramBot\Api\Types\Message;

readonly class Start implements CommandProcessInterface
{
    public function getCommand(): string
    {
        return 'start';
    }

    public function getDescription(): string
    {
        return 'Hello world!';
    }

    public function process(Message $message): CommandResult
    {
        return new CommandResult(true, 'Hello! Ask me something :)');
    }
}