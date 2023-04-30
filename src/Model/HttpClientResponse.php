<?php

namespace App\Model;

use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class HttpClientResponse
{
    private int $statusCode;
    private string $content;

    public function __construct(ResponseInterface $response)
    {
        $this->statusCode = $response->getStatusCode();
        $this->content = $response->getContent(false);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}