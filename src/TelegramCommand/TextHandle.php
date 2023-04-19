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
        $className = $chat->getCommand()->getClass();

        /** @var BotCommandCustom $telegramCommand */
        $telegramCommand = new $className($this->commandContainerService);

        return $telegramCommand->postProcess($chat, $message);
    }
}