<?php

namespace App\Model\ChatGptApi;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Usage
{
    #[SerializedName('prompt_tokens')]
    private int $promptTokens;

    #[SerializedName('completion_tokens')]
    private int $completionTokens;

    #[SerializedName('total_tokens')]
    private int $totalTokens;

    public function getPromptTokens(): int
    {
        return $this->promptTokens;
    }

    public function setPromptTokens(int $promptTokens): self
    {
        $this->promptTokens = $promptTokens;

        return $this;
    }

    public function getCompletionTokens(): int
    {
        return $this->completionTokens;
    }

    public function setCompletionTokens(int $completionTokens): self
    {
        $this->completionTokens = $completionTokens;

        return $this;
    }

    public function getTotalTokens(): int
    {
        return $this->totalTokens;
    }

    public function setTotalTokens(int $totalTokens): self
    {
        $this->totalTokens = $totalTokens;

        return $this;
    }
}