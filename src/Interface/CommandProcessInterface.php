<?php

namespace App\Interface;

use App\Model\CommandResult;
use TelegramBot\Api\Types\Message;

interface CommandProcessInterface
{
    public function getCommand(): string;

    public function getTextValue(): ?string;

    public function getDescription(): string;

    public function process(Message $message): CommandResult;
}