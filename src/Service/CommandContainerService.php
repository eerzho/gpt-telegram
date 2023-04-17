<?php

namespace App\Service;

readonly class CommandContainerService
{
    public function __construct(
        private ChatService $chatService,
        private CommandService $commandService,
    ) {
    }

    public function getChatService(): ChatService
    {
        return $this->chatService;
    }

    public function getCommandService(): CommandService
    {
        return $this->commandService;
    }
}