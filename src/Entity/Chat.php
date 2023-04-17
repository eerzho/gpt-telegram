<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $telegram_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chat_gpt_api_token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chat_gpt_model = null;

    #[ORM\OneToOne(mappedBy: 'chat', cascade: ['persist', 'remove'])]
    private ?Command $command = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegram_id;
    }

    public function setTelegramId(int $telegram_id): self
    {
        $this->telegram_id = $telegram_id;

        return $this;
    }

    public function getChatGptApiToken(): ?string
    {
        return $this->chat_gpt_api_token;
    }

    public function setChatGptApiToken(?string $chat_gpt_api_token): self
    {
        $this->chat_gpt_api_token = $chat_gpt_api_token;

        return $this;
    }

    public function getChatGptModel(): ?string
    {
        return $this->chat_gpt_model;
    }

    public function setChatGptModel(?string $chat_gpt_model): self
    {
        $this->chat_gpt_model = $chat_gpt_model;

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function setCommand(Command $command): self
    {
        // set the owning side of the relation if necessary
        if ($command->getChat() !== $this) {
            $command->setChat($this);
        }

        $this->command = $command;

        return $this;
    }
}
