<?php

namespace App\EventListener;

use App\Constant\TelegramSystemMessageText;
use App\Message\SendRequestToGpt;
use App\Service\ChatTService;
use App\Service\TelegramApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

readonly class MessageFailedListener
{
    public function __construct(
        private ChatTService $chatTService,
        private TelegramApiService $telegramApiService,
        private EntityManagerInterface $manager
    ) {
    }

    #[AsEventListener(event: WorkerMessageFailedEvent::class)]
    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $throwable = $event->getThrowable();
        $envelope = $event->getEnvelope();
        $message = $envelope->getMessage();

        if ($throwable instanceof \Exception &&
            $message instanceof SendRequestToGpt &&
            !$event->getEnvelope()->last(RedeliveryStamp::class)) {

            $chatT = $this->chatTService->getChatByTelegramId($message->getChatT()->getTelegramId())
                ->setIsGptProcess(false);

            if ($this->chatTService->save($chatT)) {
                $this->manager->flush();

                $this->telegramApiService->deleteMessage($chatT, $message->getTWaitMessage()->getMessageId());
                $this->telegramApiService->sendMessage($chatT, TelegramSystemMessageText::QUEUE_ERROR->value);
            }

        }
    }
}