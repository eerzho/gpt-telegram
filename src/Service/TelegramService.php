<?php

namespace App\Service;

use App\Constant\TelegramCommandRegistry;
use App\Entity\ChatT;
use App\Entity\MessageT;
use App\TelegramCommand\BotCommandCustom;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

readonly class TelegramService
{
    public function __construct(
        private TelegramApiClient $client,
        private CommandContainerService $commandContainerService,
        private ChatTService $chatTService,
        private MessageTService $messageTService,
        private ChatGptService $chatGptService,
        private EntityManagerInterface $manager,
        private EncryptionService $encryptionService
    ) {
    }

    public function answerByWebhook(): void
    {
        array_map(function (string $commandClass) {
            /** @var BotCommandCustom $command */
            $command = new $commandClass($this->commandContainerService);

            $this->client->command($command->getCommand(), function (Message $message) use ($command) {
                $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

                $this->manager->getConnection()->beginTransaction();
                $resultText = 'Something went wrong';
                try {

                    if ($command->process($chatT, $message, $resultText)) {
                        $this->manager->flush();
                        $this->manager->getConnection()->commit();
                    }

                } catch (Exception $exception) {

                    $this->manager->getConnection()->rollBack();
                    $resultText = sprintf('Server return %d code', $exception->getCode());
                }

                $this->sendMessage($message, $resultText);
            });

        }, TelegramCommandRegistry::getListenCommands());

        $this->client->on(function (Update $update) {
            $message = $update->getMessage() ?? $update->getEditedMessage();
            if ($message) {
                $this->messageProcess($message);
            }
        }, function () {
            return true;
        });

        $this->client->run();
    }

    public function getUpdates(): void
    {
        $this->client->getBot()->deleteWebhook();

        foreach ($this->client->getBot()->getUpdates() as $update) {
            $message = $update->getMessage() ?? $update->getEditedMessage();
            if ($message) {
                $this->messageProcess($message);
            }
        }
    }

    public function setWebhook(string $url): void
    {
        $this->client->getBot()->setWebhook($url);
    }

    public function setCommands(): void
    {
        $this->client->getBot()->setMyCommands(
            array_map(function (string $commandClass) {
                return new $commandClass();
            }, TelegramCommandRegistry::getShowCommands())
        );
    }

    private function messageProcess(Message $message): void
    {
        $waiMessage = $this->client->getBot()->sendMessage(
            $message->getChat()->getId(),
            "I'm diving into the depths of my algorithms..."
        );
        $this->client->getBot()->sendChatAction($message->getChat()->getId(), 'typing');

        $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());
        $resultText = "Seriously?\nI will not accept this message :)";
        foreach ($this->getMessageType() as $type => $response) {
            if ($message->$type()) {
                if (is_callable($response)) {
                    $resultText = $response($chatT, $message);
                }
                break;
            }
        }

        $this->client->getBot()->deleteMessage($waiMessage->getChat()->getId(), $waiMessage->getMessageId());
        $this->sendMessage($message, $resultText);
    }

    private function getMessageType(): array
    {
        return [
            'getText' => function (ChatT $chatT, Message $message) {
                return $this->textProcess($chatT, $message);
            },
        ];
    }

    private function sendMessage(Message $message, $replyText): Message
    {
        return $this->client->getBot()->sendMessage(
            $message->getChat()->getId(),
            $replyText,
            parseMode: 'Markdown',
            replyToMessageId: $message->getMessageId()
        );
    }

    private function textProcess(ChatT $chatT, Message $message): string
    {
        if ($chatT->getCommandT()->isActive()) {
            $resultText = $this->getCommandResult($chatT, $message);
        } else {
            $resultText = $this->getTextResult($chatT, $message);
        }

        return $resultText;
    }

    private function getCommandResult(ChatT $chatT, Message $message): string
    {
        $className = $chatT->getCommandT()->getClass();

        /** @var BotCommandCustom $telegramCommand */
        $telegramCommand = new $className($this->commandContainerService);

        $this->manager->getConnection()->beginTransaction();
        $resultText = 'Something went wrong';
        try {

            if ($telegramCommand->postProcess($chatT, $message, $resultText)) {
                $this->manager->flush();
                $this->manager->getConnection()->commit();
            }
        } catch (Exception $exception) {

            $this->manager->getConnection()->rollBack();
            $resultText = sprintf('Server return %d code', $exception->getCode());
        }

        return $resultText;
    }

    private function getTextResult(ChatT $chatT, Message $message): string
    {
        $this->manager->getConnection()->beginTransaction();
        $resultText = 'Something went wrong';
        try {

            $messageTs = $chatT->getMessageTs()->getValues();

            $userMessage = (new MessageT())
                ->setRole('user')
                ->setContent($this->encryptionService->encrypt($message->getText()))
                ->setChatT($chatT);

            $messageTs[] = $userMessage;

            $botMessageContent = $this->chatGptService->sendMessages($messageTs, $chatT);
            $botMessage = (new MessageT())
                ->setRole('assistant')
                ->setContent($this->encryptionService->encrypt($botMessageContent))
                ->setChatT($chatT);

            if ($this->messageTService->save($userMessage) &&
                $this->messageTService->save($botMessage)) {

                $this->manager->flush();
                $this->manager->getConnection()->commit();

                $resultText = $botMessageContent;
            }

        } catch (GuzzleException $exception) {

            $this->manager->getConnection()->rollBack();
            $resultText = sprintf('ChatGpt Api return %d code', $exception->getCode());

        } catch (Exception $exception) {

            $this->manager->getConnection()->rollBack();
            $resultText = sprintf('Server return %d code', $exception->getCode());
        }

        return $resultText;
    }
}