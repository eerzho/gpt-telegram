<?php

namespace App\EventListener;

use App\Message\SendRequestToGpt;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class DelayedMessageListener
{
    private int $delayInSeconds = 5;

    #[AsEventListener(event: WorkerMessageHandledEvent::class)]
    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        if ($message instanceof SendRequestToGpt) {
            sleep($this->delayInSeconds);
        }
    }
}