<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;

class TelegramApiClient extends Client
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        parent::__construct($this->parameterBag->get('app.api.telegram'));
    }

    public function getBot(): BotApi
    {
        return $this->api;
    }
}