<?php

namespace App\TelegramCommand;

use App\Entity\ChatT;
use App\Entity\Report;
use TelegramBot\Api\Types\Message;

class BugTrack extends BotCommandCustom
{
    protected $command = 'bugtrack';

    protected $description = 'Write if you find a bug';

    public function process(ChatT $chatT, Message $message, string &$resultText = ''): bool
    {
        $resultText = 'Describe the problem';

        return $this->commandContainerService->getCommandTService()->startCommand($chatT->getCommandT(), self::class);
    }

    public function postProcess(ChatT $chatT, Message $message, string &$resultText = ''): bool
    {
        $resultText = "We will definitely solve this problem!\nThank you for helping us improve the bot";
        $report = (new Report())->setText($message->getText())->setChatT($chatT);

        return $this->commandContainerService->getReportService()->save($report);
    }
}