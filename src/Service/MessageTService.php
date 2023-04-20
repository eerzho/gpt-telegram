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

    public function save(MessageT $messageT): bool
    {
        return $this->messageTRepository->save($messageT);
    }

    public function removeAllByChat(ChatT $chatT): bool
    {
        return $this->messageTRepository->removeAllByChatId($chatT->getId());
    }
}