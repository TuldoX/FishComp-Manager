<?php
namespace App\Entity;

class Species{
    private string $name;
    private float $maxlength;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMaxlength(): float
    {
        return $this->maxlength;
    }

    public function setMaxlength(float $maxlength): void
    {
        $this->maxlength = $maxlength;
    }
}