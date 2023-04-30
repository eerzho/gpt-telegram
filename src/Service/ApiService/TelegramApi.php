<?php

namespace App\Service\ApiService;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;

class TelegramApi extends Client
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        parent::__construct($this->parameterBag->get('app.api.telegram'));
    }

    public function getBotApi(): BotApi
    {
        return $this->api;
    }
}