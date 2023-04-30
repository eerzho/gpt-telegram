<?php

namespace App\Model;

readonly class CommandResult
{
    public function __construct(private bool $isSuccess = true, private string $text = '')
    {
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function getText(): string
    {
        return $this->text;
    }
}