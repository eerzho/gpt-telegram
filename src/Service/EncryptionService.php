<?php

namespace App\Service;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EncryptionService
{
    private Key $key;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->key = Key::loadFromAsciiSafeString($this->parameterBag->get('app.encryption.key'));
    }

    public function encrypt(string $message): string
    {
        return Crypto::encrypt($message, $this->key);
    }

    public function decrypt(string $message): string
    {
        return Crypto::decrypt($message, $this->key);
    }
}