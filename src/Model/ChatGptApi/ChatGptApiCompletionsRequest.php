<?php

namespace App\Model\ChatGptApi;

readonly class ChatGptApiCompletionsRequest
{
    /**
     * @param string|null $token
     * @param string|null $model
     * @param array<array{role: string, content: string}> $messages
     */
    public function __construct(private ?string $token, private ?string $model, private array $messages)
    {
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @return array<array{role: string, content: string}>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}