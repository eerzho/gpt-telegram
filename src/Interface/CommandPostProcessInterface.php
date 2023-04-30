<?php

namespace App\Interface;

use App\Entity\ChatT;
use App\Model\CommandResult;
use TelegramBot\Api\Types\Message;

interface CommandPostProcessInterface
{
    public function postProcess(ChatT $chatT, Message $message): CommandResult;
}