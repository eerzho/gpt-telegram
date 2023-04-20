<?php

namespace App\Service;

use App\Entity\ChatT;
use App\Entity\MessageT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChatGptService
{
    private Client $client;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly EncryptionService $encryptionService
    ) {
        $this->client = new Client();
    }

    /**
     * @param MessageT[] $messageTs
     * @param ChatT $chat
     * @return string
     * @throws GuzzleException
     */
    public function sendMessages(array $messageTs, ChatT $chat): string
    {
        $params['messages'] = array_map(function (MessageT $messageT) {
            return [
                'role' => $messageT->getRole(),
                'content' => $this->encryptionService->decrypt($messageT->getContent()),
            ];
        }, $messageTs);

        array_unshift($params['messages'], $this->getSysTemMessage());

        $response = $this->sendRequest($params, $chat);

        return $response['choices'][0]['message']['content'];
    }

    /**
     * @throws GuzzleException
     */
    private function sendRequest(array $params, ChatT $chat): array
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf(
                    'Bearer %s',
                    $chat->getChatGptApiToken() ?
                        $this->encryptionService->decrypt($chat->getChatGptApiToken()) :
                        $this->parameterBag->get('app.api.chat_gpt')
                ),
            ],
            'json' => [
                'model' => $chat->getChatGptModel(),
                'messages' => $params['messages'],
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getSysTemMessage(): array
    {
        return [
            'role' => 'system',
            'content' => $this->parameterBag->get('app.api.chat_gpt.system_message'),
        ];
    }
}