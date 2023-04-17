<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class Help extends BotCommandCustom
{
    protected $command = 'help';

    protected $description = 'Show all list of commands';

    public function process(Message $message): string
    {
        $resultText = 'Commands:';
        foreach ($this->getCommands() as $commandClass) {
            /**
             * @var BotCommandCustom $command
             */
            $command = new $commandClass();
            $resultText .= sprintf("\n\t /%s - %s", $command->getCommand(), $command->getDescription());
        }

        return $resultText;
    }
}