<?php

namespace App\Service;

use App\TelegramCommand\BotCommandCustom;
use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\RemoveModel;
use App\TelegramCommand\RemoveToken;
use App\TelegramCommand\SetModel;
use App\TelegramCommand\SetToken;
use App\TelegramCommand\Start;
use App\TelegramCommand\TextHandle;
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
        private readonly ParameterBagInterface $parameterBag,
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
            $resultText = (new TextHandle($this->commandContainerService))->process($update->getMessage());
            $this->sendMessage($update->getMessage(), $resultText);
        }, function () {
            return true;
        });

        $this->client->run();
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