<?php

namespace App\Service;

readonly class CommandContainerService
{
    public function __construct(
        private ChatTService $chatTService,
        private CommandService $commandTService,
        private MessageTService $messageTService,
        private EncryptionService $encryptionService,
    ) {
    }

    public function getChatTService(): ChatTService
    {
        return $this->chatTService;
    }

    public function getCommandTService(): CommandService
    {
        return $this->commandTService;
    }

    public function getMessageTService(): MessageTService
    {
        return $this->messageTService;
    }

    public function getEncryptionService(): EncryptionService
    {
        return $this->encryptionService;
    }
}