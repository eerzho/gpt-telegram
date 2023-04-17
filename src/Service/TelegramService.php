<?php

namespace App\Service;

use App\Entity\Chat;
use App\TelegramCommand\BotCommandCustom;
use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\RemoveModel;
use App\TelegramCommand\RemoveToken;
use App\TelegramCommand\SetModel;
use App\TelegramCommand\SetToken;
use App\TelegramCommand\Start;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

class TelegramService
{
    private BotApi $api;
    private Client $client;

    private array $commands;

    public function __construct(
        private readonly ChatGptService $chatGptService,
        private readonly ParameterBagInterface $parameterBag,
        private readonly ChatService $chatService,
        private readonly CommandService $commandService,
        private readonly CommandContainerService $commandContainerService
    ) {
        $this->api = new BotApi($this->parameterBag->get('app.api.telegram'));
        $this->client = new Client($this->parameterBag->get('app.api.telegram'));
        $this->commands = [
            Start::class,
            Help::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            Cancel::class,
        ];
    }

    public function answerByWebhook(): void
    {
        foreach ($this->getCommands() as $commandClass) {
            $command = new $commandClass($this->commandContainerService);
            $this->client->command($command->getCommand(), function (Message $message) use ($command) {
                $this->sendMessage($message, $command->process($message));
            });
        }

        $this->client->on(function (Update $update) {

            $chat = $this->chatService->saveId($update->getMessage()->getChat()->getId());

            if ($chat->getCommand()->isActive()) {
                $this->prepareCommand($chat, $update->getMessage());
            } else {
                $this->reply($update->getMessage());
            }
        }, function () {
            return true;
        });

        $this->client->run();
    }

    private function prepareCommand(Chat $chat, Message $message): void
    {
        switch ($chat->getCommand()->getName()) {
            case 'settoken':
                $this->chatService->saveToken($chat->getTelegramId(), $message->getText());
                break;
            case 'setmodel':
                $this->chatService->saveModel($chat->getTelegramId(), $message->getText());
                break;
        }

        $this->commandService->stopCommand($chat->getCommand());
        $this->sendMessage($message, $this->chatService->getChatSettingsForTelegram($chat));
    }

    private function reply(Message $message): void
    {
        $chat = $this->chatService->saveId($message->getChat()->getId());
        $resultMessage = $this->chatGptService->sendMessage($message->getText()."\n", $chat);

        $this->sendMessage($message, $resultMessage);
    }

    private function sendMessage(Message $message, $replyText): void
    {
        $this->api->sendMessage($message->getChat()->getId(), $replyText, replyToMessageId: $message->getMessageId());
    }

    public function setWebhook(string $url): void
    {
        $this->api->setWebhook($url);
    }

    public function setCommands(): mixed
    {
        $commands = [];
        foreach ($this->getCommands() as $commandClass) {
            $commands[] = new $commandClass();
        }

        return $this->api->setMyCommands($commands);
    }

    /**
     * @return BotCommandCustom[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}