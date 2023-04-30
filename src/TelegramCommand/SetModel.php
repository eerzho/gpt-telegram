<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use App\Interface\CommandPostProcessInterface;
use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use App\Service\ChatTService;
use App\Service\CommandTService;
use TelegramBot\Api\Types\Message;

readonly class SetModel implements CommandProcessInterface, CommandPostProcessInterface
{
    public function __construct(private ChatTService $chatTService, private CommandTService $commandTService)
    {
    }

    public function getCommand(): string
    {
        return 'setmodel';
    }

    public function getDescription(): string
    {
        return 'Set your model';
    }

    public function process(Message $message): CommandResult
    {
        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        return new CommandResult(
            $this->commandTService->startCommand($chatT->getCommandT(), self::class),
            'Send your model or use the /cancel command to cancel'
        );
    }

    public function postProcess(ChatT $chatT, Message $message): CommandResult
    {
        return new CommandResult(
            $this->chatTService->save($chatT->setChatGptModel($message->getText())) &&
            $this->commandTService->stopCommand($chatT->getCommandT()),
            $this->chatTService->getChatSettingsForTelegram($chatT)
        );
    }
}