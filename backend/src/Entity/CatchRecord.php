<?php

namespace App\Entity;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class CatchRecord implements JsonSerializable {
    private UuidInterface $id;
    private string $species;
    private float $points;
    private UuidInterface $competitorId;

    public function setSpecies(string $species): void
    {
        $this->species = $species;
    }

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setPoints(float $points): void
    {
        $this->points = $points;
    }

    public function getPoints(): float
    {
        return $this->points;
    }

    public function setCompetitorId(UuidInterface $competitorId): void
    {
        $this->competitorId = $competitorId;
    }

    public function getCompetitorId(): UuidInterface
    {
        return $this->competitorId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'species' => $this->species,
            'points' => $this->points
        ];
    }
}