<?php
namespace App\Entity;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use JsonSerializable;

class Referee implements JsonSerializable {
    private  UuidInterface $id;
    private string $first_name;
    private string $last_name;

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

    public function jsonSerialize(): array {
        return [
            'id' => (string) $this->id,
            'first_name' => $this->first_name,
            'last_name'=> $this->last_name,
        ];
    }
}