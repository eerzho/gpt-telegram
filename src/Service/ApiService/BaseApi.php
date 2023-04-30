<?php

namespace App\Service\ApiService;

use App\Exception\BadRequestErrorException;
use App\Exception\ServerErrorException;
use App\Model\HttpClientResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class BaseApi
{
    protected HttpClientInterface $client;

    protected SerializerInterface $serializer;

    protected string $apiPrefix = '';

    #[Required]
    public function setClient(HttpClientInterface $client): self
    {
        $this->client = $client->withOptions($this->getClientOptions());

        return $this;
    }

    #[Required]
    public function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }

    abstract protected function getClientOptions(): array;

    protected function get(string $url, array $params): HttpClientResponse
    {
        return $this->request(Request::METHOD_GET, $url, $params);
    }

    protected function post(string $url, array $params): HttpClientResponse
    {
        return $this->request(Request::METHOD_POST, $url, $params);
    }

    private function request(string $method, string $url, array $params): HttpClientResponse
    {
        $response = new HttpClientResponse($this->client->request($method, $this->apiPrefix.$url, $params));

        $this->handleError($response, $url);

        return $response;
    }

    private function handleError(HttpClientResponse $response, $url): void
    {
        if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST &&
            $response->getStatusCode() < Response::HTTP_INTERNAL_SERVER_ERROR) {
            throw new BadRequestErrorException($this::class, $response->getStatusCode(), $url, $response->getContent());
        }

        if ($response->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            throw new ServerErrorException($this::class, $response->getStatusCode(), $url, $response->getContent());
        }
    }
}