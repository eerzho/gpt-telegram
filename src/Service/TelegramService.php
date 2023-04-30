<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\MessageT;
use App\Interface\CommandPostProcessInterface;
use App\Interface\CommandProcessInterface;
use App\Message\SendRequestToGpt;
use App\Service\ApiService\TelegramApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Messenger\MessageBusInterface;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

readonly class TelegramService
{
    private ServiceLocator $commandLocator;

    public function __construct(
        private TelegramApi $telegramApi,
        private ChatTService $chatTService,
        private EntityManagerInterface $manager,
        private MessageBusInterface $messageBus,
        private ChatGptApiService $chatGptApiService,
        private EncryptionService $encryptionService,
        private MessageTService $messageTService,
        #[TaggedLocator('app.command')] ServiceLocator $commandLocator
    ) {
        $this->commandLocator = $commandLocator;
    }

    public function runBot(): void
    {
        $this->listenCommand();

        $this->listenText();

        $this->telegramApi->run();
    }

    private function listenCommand(): void
    {
        foreach ($this->commandLocator->getProvidedServices() as $commandClass) {
            $command = $this->commandLocator->get($commandClass);
            if ($command instanceof CommandProcessInterface) {
                $this->telegramApi->command($command->getCommand(), function (Message $message) use ($command) {
                    $commandResult = $command->process($message);
                    if ($commandResult->isSuccess()) {
                        $this->telegramApi->getBotApi()->sendMessage(
                            $message->getChat()->getId(),
                            $commandResult->getText(),
                            replyToMessageId: $message->getMessageId()
                        );
                        $this->manager->flush();
                    }
                });
            }
        }
    }

    private function listenText(): void
    {
        $this->telegramApi->on(function (Update $update) {
            if (!$update->getMessage()) {
                return;
            }

            if (!$update->getMessage()->getText()) {
                $this->telegramApi->getBotApi()->sendMessage(
                    $update->getMessage()->getChat()->getId(),
                    "Seriously?\nI will not accept this message :)",
                    replyToMessageId: $update->getMessage()->getMessageId()
                );

                return;
            }

            $chatT = $this->chatTService->getChatByTelegramId($update->getMessage()->getChat()->getId());

            if ($chatT->getCommandT()->isActive()) {
                $this->telegramApi->getBotApi()->sendMessage(
                    $update->getMessage()->getChat()->getId(),
                    $this->getCommandPostProcessResult($chatT, $update->getMessage()),
                    replyToMessageId: $update->getMessage()->getMessageId()
                );

                return;
            }

            $waiMessage = $this->telegramApi->getBotApi()->sendMessage(
                $update->getMessage()->getChat()->getId(),
                "I'm diving into the depths of my algorithms...",
            );
            $this->telegramApi->getBotApi()->sendChatAction($update->getMessage()->getChat()->getId(), 'typing');

            $this->messageBus->dispatch(new SendRequestToGpt($chatT, $update->getMessage(), $waiMessage));
        }, function () {
            return true;
        });
    }

    private function getCommandPostProcessResult(ChatT $chatT, Message $message): string
    {
        $command = $this->commandLocator->get($chatT->getCommandT()->getClass());
        $resultText = 'Something went wrong';
        if ($command instanceof CommandPostProcessInterface) {
            $commandResult = $command->postProcess($chatT, $message);
            if ($commandResult->isSuccess()) {
                $this->manager->flush();
                $resultText = $commandResult->getText();
            }
        }

        return $resultText;
    }

    public function sendMessageToCpt(ChatT $chatT, Message $message, Message $waitMessage): void
    {
        $gptMessage = $this->chatGptApiService->getAssistantMessage($chatT, $message);

        $userMessage = (new MessageT())
            ->setChatT($chatT)
            ->setRole('user')
            ->setContent($this->encryptionService->encrypt($message->getText()));
        $botMessage = (new MessageT())
            ->setChatT($chatT)
            ->setRole('assistant')
            ->setContent($this->encryptionService->encrypt($gptMessage->getContent()));

        if ($this->messageTService->save($userMessage) &&
            $this->messageTService->save($botMessage)) {
            $this->telegramApi->getBotApi()->deleteMessage(
                $waitMessage->getChat()->getId(),
                $waitMessage->getMessageId()
            );
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                $gptMessage->getContent(),
                parseMode: 'Markdown',
                replyToMessageId: $message->getMessageId()
            );
            $this->manager->flush();
        }
    }
}