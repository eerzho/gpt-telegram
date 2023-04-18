<?php

namespace App\Service;

use App\Entity\Chat;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChatGptService
{
    private Client $client;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->client = new Client();
    }

    public function sendMessage(string $message, Chat $chat): string
    {
        $params = [
            'prompt' => $message,
            'temperature' => $chat->getTemperature(),
            'max_tokens' => $chat->getMaxTokens(),
            'stop' => ['\n'],
        ];

        $resultMessage = '';
        while (true) {
            $result = $this->sendRequest($params, $chat);
            $resultMessage .= $result['choices'][0]['text'];

            if ($result['choices'][0]['finish_reason'] === 'length') {
                $params['prompt'] .= $result['choices'][0]['text'];
            } else {
                break;
            }
        }

        return $resultMessage;
    }

    private function sendRequest(array $params, Chat $chat): array
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf(
                    'Bearer %s',
                    $chat->getChatGptApiToken() ?? $this->parameterBag->get('app.api.chat_gpt')
                ),
            ],
            'json' => [
                'model' => $chat->getChatGptModel(),
                'prompt' => $params['prompt'],
                'temperature' => $params['temperature'],
                'max_tokens' => $params['max_tokens'],
                'stop' => $params['stop'],
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}