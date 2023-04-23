<?php

namespace App\Entity;

use App\Repository\ChatTRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatTRepository::class)]
class ChatT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $telegram_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chat_gpt_api_token = null;

    #[ORM\Column(length: 255, options: ['default' => 'gpt-3.5-turbo'])]
    private ?string $chat_gpt_model = 'gpt-3.5-turbo';

    #[ORM\OneToMany(mappedBy: 'chat_t', targetEntity: MessageT::class)]
    private Collection $message_ts;

    #[ORM\OneToOne(mappedBy: 'chat_t', cascade: ['persist', 'remove'])]
    private CommandT $command_t;

    #[ORM\OneToMany(mappedBy: 'chat_t', targetEntity: Report::class, orphanRemoval: true)]
    private Collection $reports;

    public function __construct()
    {
        $this->message_ts = new ArrayCollection();
        $this->command_t = new CommandT();
        $this->reports = new ArrayCollection();
    }

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

    public function getChatGptModel(): string
    {
        return $this->chat_gpt_model;
    }

    public function setChatGptModel(?string $chat_gpt_model): self
    {
        $this->chat_gpt_model = $chat_gpt_model ?? 'gpt-3.5-turbo';

        return $this;
    }

    /**
     * @return Collection<int, MessageT>
     */
    public function getMessageTs(): Collection
    {
        return $this->message_ts;
    }

    public function addMessageT(MessageT $messageT): self
    {
        if (!$this->message_ts->contains($messageT)) {
            $this->message_ts->add($messageT);
            $messageT->setChatT($this);
        }

        return $this;
    }

    public function removeMessageT(MessageT $messageT): self
    {
        if ($this->message_ts->removeElement($messageT)) {
            // set the owning side to null (unless already changed)
            if ($messageT->getChatT() === $this) {
                $messageT->setChatT(null);
            }
        }

        return $this;
    }

    public function getCommandT(): CommandT
    {
        return $this->command_t;
    }

    public function setCommandT(CommandT $command_t): self
    {
        // set the owning side of the relation if necessary
        if ($command_t->getChatT() !== $this) {
            $command_t->setChatT($this);
        }

        $this->command_t = $command_t;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setChatT($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getChatT() === $this) {
                $report->setChatT(null);
            }
        }

        return $this;
    }
}
