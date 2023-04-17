<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class Start extends BotCommandCustom
{
    protected $command = 'start';

    protected $description = 'Hello world!';

    public function process(Message $message): string
    {
        return 'Hello everyone, this bot is designed for convenient and fast communication with ChatGpt. Here, you can customize the settings of the ChatGpt API as per your convenience.';
    }
}