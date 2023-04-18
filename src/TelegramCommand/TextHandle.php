<?php

namespace App\TelegramCommand;

use App\Entity\Chat;
use GuzzleHttp\Exception\GuzzleException;
use TelegramBot\Api\Types\Message;

class TextHandle extends BotCommandCustom
{
    protected $command = '';
    protected $description = '';

    public function process(Message $message): string
    {
        $chat = $this->commandContainerService->getChatService()->saveId($message->getChat()->getId());

        if ($chat->getCommand()->isActive()) {
            $resultText = $this->getCommandResult($chat, $message);
        } else {
            $resultText = $this->getChatGptResult($chat, $message);
        }

        return $resultText;
    }

    public function getChatGptResult(Chat $chat, Message $message): string
    {
        try {
            $resultText = $this->commandContainerService->getChatGptService()->sendMessage(
                $message->getText()."\n",
                $chat
            );
        } catch (GuzzleException $exception) {
            $resultText = sprintf('ChatGpt Api return %d code', $exception->getCode());
        }

        return $resultText;
    }

    private function getCommandResult(Chat $chat, Message $message): string
    {
        switch ($chat->getCommand()->getName()) {
            case 'settoken':
                $this->commandContainerService->getChatService()->saveToken(
                    $chat->getTelegramId(),
                    $message->getText()
                );
                break;
            case 'setmodel':
                $this->commandContainerService->getChatService()->saveModel(
                    $chat->getTelegramId(),
                    $message->getText()
                );
                break;
            case 'settemperature':
                $this->commandContainerService->getChatService()->saveTemperature(
                    $chat->getTelegramId(),
                    $message->getText()
                );
                break;
            case 'setmaxtokens':
                $this->commandContainerService->getChatService()->saveMaxTokens(
                    $chat->getTelegramId(),
                    $message->getText()
                );
                break;
        }

        $this->commandContainerService->getCommandService()->stopCommand($chat->getCommand());

        return $this->commandContainerService->getChatService()->getChatSettingsForTelegram($chat);
    }
}