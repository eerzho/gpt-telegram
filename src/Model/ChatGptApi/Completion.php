<?php

namespace App\Model\ChatGptApi;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Completion
{
    #[SerializedName('id')]
    private string $id;

    #[SerializedName('object')]
    private string $object;

    #[SerializedName('created')]
    private int $created;

    #[SerializedName('model')]
    private string $model;

    #[SerializedName('usage')]
    private Usage $usage;

    #[SerializedName('choices')]
    private array $choices;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getUsage(): Usage
    {
        return $this->usage;
    }

    public function setUsage(Usage $usage): self
    {
        $this->usage = $usage;

        return $this;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public static function postDeserializeChoices(array $choices, string $format): array
    {
        $deserializedChoices = [];
        foreach ($choices as $choice) {
            $message = (new Message())->setRole($choice['message']['role'])
                ->setContent($choice['message']['content']);

            $deserializedChoices[] = (new Choice())->setMessage($message)
                ->setFinishReason($choice['finish_reason'])
                ->setIndex($choice['index']);
        }

        return $deserializedChoices;
    }
}