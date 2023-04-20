<?php

namespace App\Entity;

use App\Repository\CommandTRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandTRepository::class)]
class CommandT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $active = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $class = null;

    #[ORM\OneToOne(inversedBy: 'command_t', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChatT $chat_t = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getChatT(): ?ChatT
    {
        return $this->chat_t;
    }

    public function setChatT(ChatT $chat_t): self
    {
        $this->chat_t = $chat_t;

        return $this;
    }
}
