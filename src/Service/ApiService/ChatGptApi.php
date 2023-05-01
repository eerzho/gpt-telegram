<?php

namespace App\Service\ApiService;

use App\Model\ChatGptApi\ChatGptApiCompletionsRequest;
use App\Model\ChatGptApi\Completion;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ChatGptApi extends BaseApi
{
    protected string $apiPrefix = 'v1';

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    protected function getClientOptions(): array
    {
        return [
            'base_uri' => 'https://api.openai.com',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];
    }

    public function completion(ChatGptApiCompletionsRequest $request): Completion
    {
        $this->token = $request->getToken();

        $messages = $request->getMessages();
        array_unshift($messages, $this->getSystemMessage());

        $params = [
            'headers' => [
                'Authorization' => sprintf(
                    'Bearer %s',
                    $request->getToken() ?? $this->parameterBag->get('app.api.chat_gpt')
                ),
            ],
            'json' => [
                'model' => $request->getModel() ?? 'gpt-3.5-turbo',
                'messages' => $messages,
            ],
        ];

        $response = $this->post('/chat/completions', $params);

        return $this->serializer->deserialize(
            $response->getContent(),
            Completion::class,
            'json',
            context: [
                AbstractNormalizer::CALLBACKS => [
                    'choices' => [Completion::class, 'postDeserializeChoices'],
                ],
            ]
        );
    }

    /**
     * @return array{role: string, content: string}
     */
    private function getSystemMessage(): array
    {
        return [
            'role' => 'system',
            'content' => $this->parameterBag->get('app.api.chat_gpt.system_message'),
        ];
    }
}