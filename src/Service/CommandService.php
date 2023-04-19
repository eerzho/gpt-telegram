<?php

namespace App\Service;

use App\Entity\Command;
use App\Repository\CommandRepository;

readonly class CommandService
{
    public function __construct(private CommandRepository $commandRepository)
    {
    }

    public function startCommand(Command $command, string $commandClass): void
    {
        $command->setClass($commandClass)->setActive(true);

        $this->commandRepository->save($command, true);
    }

    public function stopCommand(Command $command): void
    {
        $command->setClass(null)->setActive(false);

        $this->commandRepository->save($command, true);
    }
}