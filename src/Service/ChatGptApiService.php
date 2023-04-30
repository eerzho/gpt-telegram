<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\MessageT;
use App\Model\ChatGptApi\ChatGptApiCompletionsRequest;
use App\Service\ApiService\ChatGptApi;
use TelegramBot\Api\Types\Message;

readonly class ChatGptApiService
{
    public function __construct(
        private ChatGptApi $chatGptApi,
        private EncryptionService $encryptionService
    ) {
    }

    public function getAssistantMessage(ChatT $chatT, Message $message): \App\Model\ChatGptApi\Message
    {
        $completion = $this->chatGptApi->completion(
            new ChatGptApiCompletionsRequest(
                $chatT->getChatGptApiToken() ?
                    $this->encryptionService->decrypt($chatT->getChatGptApiToken()) : null,
                $chatT->getChatGptModel(),
                $this->generateMessages($chatT, $message)
            )
        );
        $choices = $completion->getChoices();

        return reset($choices)->getMessage();
    }

    /**
     * @param ChatT $chatT
     * @param Message $message
     * @return array<array{role: string, content: string}>
     */
    private function generateMessages(ChatT $chatT, Message $message): array
    {
        $messages = array_map(function (MessageT $messageT) {
            return [
                'role' => $messageT->getRole(),
                'content' => $this->encryptionService->decrypt($messageT->getContent()),
            ];
        }, $chatT->getMessageTs()->getValues());

        $messages[] = [
            'role' => 'user',
            'content' => $message->getText(),
        ];

        return $messages;
    }
}