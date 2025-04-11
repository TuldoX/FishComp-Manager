<?php

namespace App\Entity;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class Competitor implements JsonSerializable {
    private  UuidInterface $id;
    private string $name;
    private int $location;
    private UuidInterface $refereeId;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param int $location
     * @param UuidInterface $refereeId
     */
    public function __construct(UuidInterface $id, string $name, int $location, UuidInterface $refereeId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->location = $location;
        $this->refereeId = $refereeId;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
            'name' => $this->name,
            'location' => $this->location,
            'refereeId' => (string) $this->refereeId
        ];
    }
}