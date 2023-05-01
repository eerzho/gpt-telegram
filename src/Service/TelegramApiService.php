<?php

namespace App\Service;

use App\Constant\TelegramCommandRegistry;
use App\Constant\TelegramSystemMessageText;
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
use TelegramBot\Api\Types\ArrayOfBotCommand;
use TelegramBot\Api\Types\BotCommand;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

readonly class TelegramApiService
{
    private ServiceLocator $commandLocator;

    private ReplyKeyboardMarkup $keyboard;

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
                    $this->processCommand($command, $message);
                });
            }
        }
    }

    private function listenText(): void
    {
        $this->telegramApi->on(function (Update $update) {
            if ($update->getMessage()) {
                foreach (TelegramCommandRegistry::getForKeyboard() as $commandClass) {
                    $command = $this->commandLocator->get($commandClass);
                    if ($command instanceof CommandProcessInterface &&
                        $command->getTextValue() == $update->getMessage()->getText()) {
                        $this->processCommand($command, $update->getMessage());

                        return;
                    }
                }
                $this->processText(
                    $update->getMessage(),
                    function (ChatT $chatT, Message $message, Message $waitMessage) {
                        $this->messageBus->dispatch(new SendRequestToGpt($chatT, $message, $waitMessage));
                    }
                );
            }
        }, function () {
            return true;
        });
    }

    private function processCommand(CommandProcessInterface $command, Message $message): void
    {
        $commandResult = $command->process($message);
        if ($commandResult->isSuccess()) {
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                $commandResult->getText(),
                replyToMessageId: $message->getMessageId(),
                replyMarkup: $this->getKeyboard()
            );
            $this->manager->flush();
        }
    }

    private function getCommandPostProcessResult(ChatT $chatT, Message $message): string
    {
        $command = $this->commandLocator->get($chatT->getCommandT()->getClass());
        $resultText = TelegramSystemMessageText::COMMAND_POST_PROCESS_ERROR->value;
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
            $this->messageTService->save($botMessage) &&
            $this->chatTService->save($chatT->setIsGptProcess(false))) {
            $this->telegramApi->getBotApi()->deleteMessage(
                $waitMessage->getChat()->getId(),
                $waitMessage->getMessageId()
            );
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                $gptMessage->getContent(),
                parseMode: 'Markdown',
                replyToMessageId: $message->getMessageId(),
                replyMarkup: $this->getKeyboard()
            );
            $this->manager->flush();
        }
    }

    public function deleteWebhook(): mixed
    {
        return $this->telegramApi->getBotApi()->deleteWebhook();
    }

    public function setWebhook(string $url): string
    {
        return $this->telegramApi->getBotApi()->setWebhook($url);
    }

    public function setCommands(): mixed
    {
        $commandsArray = [];
        foreach (TelegramCommandRegistry::getShowCommands() as $commandClass) {
            $command = $this->commandLocator->get($commandClass);
            if ($command instanceof CommandProcessInterface) {
                $botCommand = new BotCommand();
                $botCommand->setCommand($command->getCommand());
                $botCommand->setDescription($command->getDescription());
                $commandsArray[] = $botCommand;
            }
        }

        return $this->telegramApi->getBotApi()->setMyCommands(new ArrayOfBotCommand($commandsArray));
    }

    public function getUpdates(): void
    {
        $this->deleteWebhook();

        foreach ($this->telegramApi->getBotApi()->getUpdates() as $update) {
            if (!$update->getMessage()) {
                return;
            }
            $this->processText($update->getMessage(), function (ChatT $chatT, Message $message, Message $waitMessage) {
                $this->sendMessageToCpt($chatT, $message, $waitMessage);
            });
        }
    }

    private function processText(Message $message, callable $sendMessage): void
    {
        if (!$message->getText()) {
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                TelegramSystemMessageText::MESSAGE_TYPE_ERROR->value,
                replyToMessageId: $message->getMessageId(),
                replyMarkup: $this->getKeyboard()
            );

            return;
        }

        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

        if ($chatT->getCommandT()->isActive()) {
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                $this->getCommandPostProcessResult($chatT, $message),
                replyToMessageId: $message->getMessageId(),
                replyMarkup: $this->getKeyboard()
            );

            return;
        }

        if ($chatT->isIsGptProcess()) {
            $this->telegramApi->getBotApi()->sendMessage(
                $message->getChat()->getId(),
                TelegramSystemMessageText::ALREADY_PROCESSING->value,
                replyToMessageId: $message->getMessageId(),
                replyMarkup: $this->getKeyboard()
            );

            return;
        } elseif ($this->chatTService->save($chatT->setIsGptProcess(true))) {
            $this->manager->flush();
        }

        $waitMessage = $this->telegramApi->getBotApi()->sendMessage(
            $message->getChat()->getId(),
            TelegramSystemMessageText::WAIT_PROCESS->value
        );
        $this->telegramApi->getBotApi()->sendChatAction($message->getChat()->getId(), 'typing');

        $sendMessage($chatT, $message, $waitMessage);
    }

    public function getKeyboard(): ReplyKeyboardMarkup
    {
        if (isset($this->keyboard)) {
            return $this->keyboard;
        }

        $keyboardArray[] = [];
        $keyboardCountInOneLine = 4;
        foreach (TelegramCommandRegistry::getForKeyboard() as $commandClass) {
            $command = $this->commandLocator->get($commandClass);
            if ($command instanceof CommandProcessInterface && $command->getTextValue()) {

                if (count(end($keyboardArray)) === $keyboardCountInOneLine) {
                    $keyboardArray[] = [];
                }

                $keyboardArray[count($keyboardArray) - 1][] = $command->getTextValue();
            }
        }
        $this->keyboard = new ReplyKeyboardMarkup($keyboardArray, oneTimeKeyboard: false);

        return $this->keyboard;
    }
}