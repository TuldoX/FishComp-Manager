<?php

namespace App\Entity;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class Competitor implements JsonSerializable {
    private  UuidInterface $id;
    private string $first_name;
    private string $last_name;
    private int $location;
    private UuidInterface $refereeId;

    private float $points;

    public function getPoints(): float
    {
        return $this->points;
    }

    public function setPoints(float $points): void
    {
        $this->points = $points;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getLastName():string {
        return $this->last_name;
    }

    public function setLastName(string $last_name) : void {
        $this->last_name = $last_name;
    }

    public function getLocation(): int
    {
        return $this->location;
    }

    public function setLocation(int $location): void
    {
        $this->location = $location;
    }

    public function getRefereeId(): UuidInterface
    {
        return $this->refereeId;
    }

    public function setRefereeId(UuidInterface $refereeId): void
    {
        $this->refereeId = $refereeId;
    }

    public function jsonSerialize(): array {
        return [
            'id' => (string) $this->id,
            'first_name' => $this->first_name,
            'last_name'=> $this->last_name,
            'location' => $this->location,
            'points' => $this->points,
        ];
    }
}