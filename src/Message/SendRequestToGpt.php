<?php

namespace App\Message;

use App\Entity\ChatT;
use TelegramBot\Api\Types\Message;

final readonly class SendRequestToGpt
{
    public function __construct(private ChatT $chatT, private Message $tUserMessage, private Message $tWaitMessage)
    {
    }

    public function getChatT(): ChatT
    {
        return $this->chatT;
    }

    public function getTUserMessage(): Message
    {
        return $this->tUserMessage;
    }

    public function getTWaitMessage(): Message
    {
        return $this->tWaitMessage;
    }
}
