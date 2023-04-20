<?php

namespace App\Service;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EncryptionService
{
    private EncryptionKey $encryptionKey;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->encryptionKey = KeyFactory::loadEncryptionKey($this->parameterBag->get('app.encryption.key_path'));
    }

    public function encrypt(string $message): string
    {
        return Crypto::encrypt(new HiddenString($message), $this->encryptionKey);
    }

    public function decrypt(string $message): string
    {
        return Crypto::decrypt($message, $this->encryptionKey)->getString();
    }
}