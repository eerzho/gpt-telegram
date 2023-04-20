<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

class Help extends BotCommandCustom
{
    protected $command = 'help';

    protected $description = 'Show all list of commands';

    public function process(ChatT $chatT, Message $message, &$resultText = ''): bool
    {
        $resultText = 'Commands:';
        array_map(function (string $commandClass) use (&$resultText) {
            /** @var BotCommandCustom $command */
            $command = new $commandClass();
            $resultText .= sprintf("\n\t /%s - %s", $command->getCommand(), $command->getDescription());
        }, $this->getCommands());

        return true;
    }
}