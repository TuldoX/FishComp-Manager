<?php
namespace App\Entity;

use JsonSerializable;
class Species implements JsonSerializable {
    private string $name;
    private float $max_length;

    private int $id;

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setMaxlength(float $maxlength): void
    {
        $this->max_length = $maxlength;
    }

    public function getMaxlength(): float
    {
        return $this->max_length;
    }

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'max_length' => $this->max_length,
        ];
    }
}