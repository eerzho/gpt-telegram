<?php

namespace App\TelegramCommand;

use App\Constant\TelegramCommandRegistry;
use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use TelegramBot\Api\Types\Message;

readonly class Help implements CommandProcessInterface
{
    private ServiceLocator $commandLocator;

    public function __construct(#[TaggedLocator('app.command')] ServiceLocator $commandLocator)
    {
        $this->commandLocator = $commandLocator;
    }

    public function getCommand(): string
    {
        return 'help';
    }

    public function getDescription(): string
    {
        return 'Show all list of commands';
    }

    public function process(Message $message): CommandResult
    {
        $text = 'Commands:';
        foreach (TelegramCommandRegistry::getShowCommands() as $commandClass) {
            $command = $this->commandLocator->get($commandClass);
            if ($command instanceof CommandProcessInterface) {
                $text .= sprintf("\n\t /%s - %s", $command->getCommand(), $command->getDescription());
            }
        }

        return new CommandResult(true, $text);
    }
}