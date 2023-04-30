<?php

namespace App\Model\ChatGptApi;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Message
{
    #[SerializedName('role')]
    private string $role;

    #[SerializedName('content')]
    private string $content;

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}