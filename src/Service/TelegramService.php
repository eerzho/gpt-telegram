<?php

namespace App\Service;

use App\Entity\Chat;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

readonly class TelegramService
{
    private BotApi $api;
    private Client $client;

    public function __construct(
        private ChatGptService $chatGptService,
        private ParameterBagInterface $parameterBag,
        private ChatService $chatService,
    ) {
        $this->api = new BotApi($this->parameterBag->get('app.api.telegram'));
        $this->client = new Client($this->parameterBag->get('app.api.telegram'));
    }

    public function answerByWebhook(): void
    {
        $this->client->command('help', function (Message $message) {
            $this->sendMessage(
                $message,
                "Commands:\n\t/setToken - Set your token\n\t/removeToken - Remove yor token\n\t/setModel - Set your model\n\t/removeModel - Remove your model"
            );
        });

        $this->client->command('setToken', function (Message $message) {
            $chat = $this->chatService->saveToken(
                $message->getChat()->getId(),
                $this->getCommandValue($message->getText())
            );
            $this->sendMessage($message, $this->getSettingsMessage($chat));
        });

        $this->client->command('removeToken', function (Message $message) {
            $chat = $this->chatService->saveToken($message->getChat()->getId(), null);
            $this->sendMessage($message, $this->getSettingsMessage($chat));
        });

        $this->client->command('setModel', function (Message $message) {
            $chat = $this->chatService->saveModel(
                $message->getChat()->getId(),
                $this->getCommandValue($message->getText())
            );
            $this->sendMessage($message, $this->getSettingsMessage($chat));
        });

        $this->client->command('removeModel', function (Message $message) {
            $chat = $this->chatService->saveModel($message->getChat()->getId(), null);
            $this->sendMessage($message, $this->getSettingsMessage($chat));
        });

        $this->client->on(function (Update $update) {
            $this->reply($update->getMessage());
        }, function () {
            return true;
        });

        $this->client->run();
    }

    private function getSettingsMessage(Chat $chat): string
    {
        return sprintf(
            "Your settings:\n\tchat id - %d\n\ttoken - %s\n\tmodel - %s",
            $chat->getTelegramId(),
            $chat->getChatGptApiToken() ?? 'API_TOKEN (default)',
            $chat->getChatGptModel() ?? sprintf('%s (default)', $this->parameterBag->get('app.api.chat_gpt.model')),
        );
    }

    private function getCommandValue($messageText): string
    {
        return preg_replace("~^/[\w-]+\s~", "", $messageText);
    }

    public function answerByUpdates(): void
    {
        $this->api->deleteWebhook();
        foreach ($this->api->getUpdates() as $update) {
            $this->reply($update->getMessage());
        }
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
}