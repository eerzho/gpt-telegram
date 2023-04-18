<?php

namespace App\Service;

use App\TelegramCommand\BotCommandCustom;
use App\TelegramCommand\Cancel;
use App\TelegramCommand\Help;
use App\TelegramCommand\RemoveMaxTokens;
use App\TelegramCommand\RemoveModel;
use App\TelegramCommand\RemoveTemperature;
use App\TelegramCommand\RemoveToken;
use App\TelegramCommand\SetMaxTokens;
use App\TelegramCommand\SetModel;
use App\TelegramCommand\SetTemperature;
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

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly CommandContainerService $commandContainerService
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
                $this->sendMessage($message, $command->process($message));
            });
        }, $this->getCommands());

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
        return $this->api->setMyCommands(
            array_map(function (string $commandClass) {
                return new $commandClass();
            }, $this->getCommands())
        );
    }

    private function getCommands(): array
    {
        return [
            Start::class,
            Help::class,
            SetToken::class,
            RemoveToken::class,
            SetModel::class,
            RemoveModel::class,
            SetTemperature::class,
            RemoveTemperature::class,
            SetMaxTokens::class,
            RemoveMaxTokens::class,
            Cancel::class,
        ];;
    }
}