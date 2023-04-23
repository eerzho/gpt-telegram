<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class Start extends BotCommandCustom
{
    protected $command = 'start';

    protected $description = 'Hello world!';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        $resultText = 'Hello! Ask me something :)';

        return true;
    }
}