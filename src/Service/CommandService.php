<?php

namespace App\Service;

use App\Entity\CommandT;
use App\Repository\CommandTRepository;

readonly class CommandService
{
    public function __construct(private CommandTRepository $commandRepository)
    {
    }

    public function startCommand(CommandT $command, string $commandClass): bool
    {
        $command->setClass($commandClass)->setActive(true);

        return $this->commandRepository->save($command, true);
    }

    public function stopCommand(CommandT $command): bool
    {
        $command->setClass(null)->setActive(false);

        return $this->commandRepository->save($command, true);
    }
}