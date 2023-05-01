<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use App\Entity\Report;
use App\Interface\CommandPostProcessInterface;
use App\Interface\CommandProcessInterface;
use App\Model\CommandResult;
use App\Service\ChatTService;
use App\Service\CommandTService;
use App\Service\ReportService;
use TelegramBot\Api\Types\Message;

readonly class BugTrack implements CommandProcessInterface, CommandPostProcessInterface
{
    public function __construct(
        private ChatTService $chatTService,
        private CommandTService $commandTService,
        private ReportService $reportService
    ) {
    }

    public function getCommand(): string
    {
        return 'bugtrack';
    }

    public function getTextValue(): ?string
    {
        return "📨";
    }

    public function getDescription(): string
    {
        return 'Write if you find a bug';
    }

    public function process(Message $message): CommandResult
    {
        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        return new CommandResult(
            $this->commandTService->startCommand($chatT->getCommandT(), self::class),
            'Describe the problem'
        );
    }

    public function postProcess(ChatT $chatT, Message $message): CommandResult
    {
        $report = (new Report())->setText($message->getText())->setChatT($chatT);

        return new CommandResult(
            $this->reportService->save($report) &&
            $this->commandTService->stopCommand($chatT->getCommandT()),
            "We will definitely solve this problem!\nThank you for helping us improve the bot"
        );
    }
}