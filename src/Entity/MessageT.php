<?php

namespace App\Entity;

use App\Repository\MessageTRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageTRepository::class)]
class MessageT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'message_ts')]
    private ?ChatT $chat_t = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getChatT(): ChatT
    {
        return $this->chat_t;
    }

    public function setChatT(ChatT $chat_t): self
    {
        $this->chat_t = $chat_t;

        return $this;
    }
}
