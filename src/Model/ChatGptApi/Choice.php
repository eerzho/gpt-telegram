<?php

namespace App\Model\ChatGptApi;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Choice
{
    #[SerializedName('message')]
    private Message $message;

    #[SerializedName('finish_reason')]
    private string $finishReason;

    #[SerializedName('index')]
    private int $index;

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getFinishReason(): string
    {
        return $this->finishReason;
    }

    public function setFinishReason(string $finishReason): self
    {
        $this->finishReason = $finishReason;

        return $this;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }
}