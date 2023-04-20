<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\MessageT;
use App\Repository\MessageTRepository;

readonly class MessageTService
{
    public function __construct(private MessageTRepository $messageTRepository)
    {
    }

    public function save(MessageT $messageT, bool $flush = true): bool
    {
        return $this->messageTRepository->save($messageT, $flush);
    }

    public function removeAllByChat(ChatT $chatT)
    {
        return $this->messageTRepository->removeAllByChatId($chatT->getId());
    }
}