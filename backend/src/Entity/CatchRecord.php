<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;

class CatchRecord{
    private Uuid $id;
    private string $species;
    private float $points;
    private Uuid $competitorId;

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setSpecies(string $species): void
    {
        $this->species = $species;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getPoints(): float
    {
        return $this->points;
    }

    public function setPoints(float $points): void
    {
        $this->points = $points;
    }

    public function getCompetitorId(): Uuid
    {
        return $this->competitorId;
    }

    public function setCompetitorId(Uuid $competitorId): void
    {
        $this->competitorId = $competitorId;
    }

}