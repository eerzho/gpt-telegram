<?php

namespace App\Service;

use App\Constant\TelegramCommandRegistry;
use App\Entity\ChatT;
use App\Entity\MessageT;
use App\TelegramCommand\BotCommandCustom;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class TelegramService
{
    private BotApi $api;

    private Client $client;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly CommandContainerService $commandContainerService,
        private readonly ChatTService $chatTService,
        private readonly MessageTService $messageTService,
        private readonly ChatGptService $chatGptService,
    ) {
        $this->api = new BotApi($this->parameterBag->get('app.api.telegram'));
        $this->client = new Client($this->parameterBag->get('app.api.telegram'));
    }

    public function answerByWebhook(): void
    {
        array_map(function (string $commandClass) {

            /** @var BotCommandCustom $command */
            $command = new $commandClass($this->commandContainerService);

            $this->client->command($command->getCommand(), function (Message $message) use ($command) {
                $chatT = $this->chatTService->getChatByTelegramId($message->getChat()->getId());

                $this->sendMessage($message, $command->process($chatT, $message));
            });

        }, TelegramCommandRegistry::getListenCommands());

        $this->client->on(function (Update $update) {
            $this->listen($update);
        }, function () {
            return true;
        });

        $this->client->run();
    }

    private function listen(Update $update): void
    {
        $chatT = $this->chatTService->getChatByTelegramId($update->getMessage()->getChat()->getId());

        if ($chatT->getCommandT()?->isActive()) {
            $resultText = $this->getCommandResult($chatT, $update->getMessage());
        } else {
            $resultText = $this->getTextResult($chatT, $update->getMessage());
        }

        $this->sendMessage($update->getMessage(), $resultText);
    }

    public function getUpdates(): void
    {
        $this->api->deleteWebhook();

        foreach ($this->api->getUpdates() as $update) {
            $this->listen($update);
        }
    }

    private function sendMessage(Message $message, $replyText): void
    {
        $this->api->sendMessage(
            $message->getChat()->getId(),
            $replyText,
            parseMode: 'Markdown',
            replyToMessageId: $message->getMessageId()
        );
    }

    public function setWebhook(string $url): void
    {
        $this->api->setWebhook($url);
    }

    public function setCommands(): mixed
    {
        return $this->api->setMyCommands(
            array_map(function (string $commandClass) {
                return new $commandClass();
            }, TelegramCommandRegistry::getShowCommands())
        );
    }

    private function getCommandResult(ChatT $chatT, Message $message): string
    {
        $className = $chatT->getCommandT()->getClass();

        /** @var BotCommandCustom $telegramCommand */
        $telegramCommand = new $className($this->commandContainerService);

        return $telegramCommand->postProcess($chatT, $message);
    }

    private function getTextResult(ChatT $chatT, Message $message): string
    {
        $messageT = (new MessageT())
            ->setRole(
                $message->getFrom()->getId() == $this->parameterBag->get('app.api.telegram.bot_id') ?
                    'assistant' :
                    'user'
            )->setContent($message->getText())
            ->setChatT($chatT);

        if ($this->messageTService->save($messageT)) {
            try {
                ;
                $resultText = $this->chatGptService->sendMessages($chatT->getMessageTs()->getValues(), $chatT);

                $gptMessage = (new MessageT())
                    ->setRole('assistant')
                    ->setContent($resultText)
                    ->setChatT($chatT);

                if (!$this->messageTService->save($gptMessage)) {
                    $resultText = 'Something went wrong';
                }

            } catch (GuzzleException $exception) {
                $resultText = sprintf('ChatGpt Api return %d code', $exception->getCode());
            }
        } else {
            $resultText = 'Something went wrong';
        }

        return $resultText;
    }
}