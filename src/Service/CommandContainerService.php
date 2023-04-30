<?php

namespace App\Service;

readonly class CommandContainerService
{
    public function __construct(
        private ChatTService $chatTService,
        private CommandTService $commandTService,
        private MessageTService $messageTService,
        private EncryptionService $encryptionService,
        private ReportService $reportService,
    ) {
    }

    public function getChatTService(): ChatTService
    {
        return $this->chatTService;
    }

    public function getCommandTService(): CommandTService
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

    public function getReportService(): ReportService
    {
        return $this->reportService;
    }
}