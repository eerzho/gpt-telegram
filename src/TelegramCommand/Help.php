<?php

namespace App\TelegramCommand;

use TelegramBot\Api\Types\Message;

class Help extends BotCommandCustom
{
    protected $command = 'help';

    protected $description = 'Show all list of commands';

    public function process(Message $message): string
    {
        $commandsClass = [
            Start::class,
            Help::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            Cancel::class,
        ];

        $resultText = 'Commands:';
        foreach ($commandsClass as $commandClass) {
            /**
             * @var BotCommandCustom $command
             */
            $command = new $commandClass();
            $resultText .= sprintf("\n\t /%s - %s", $command->getCommand(), $command->getDescription());
        }

        return $resultText;
    }
}