<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;

class Competitor{
    private  Uuid $id;
    private string $name = '';
    private string $location = '';

    private Uuid $refereeId;

    public function getId(): ?Uuid{
        return $this->id;
    }

    public function setId(Uuid $id): void{
        $this->id = $id;
    }

    public function getName(): ?string{
        return $this->name;
    }

    public function setName(string $name): void{
        $this->name = $name;
    }

    public function getLocation(): ?string{
        return $this->location;
    }

    public function setLocation(string $location): void{
        $this->location = $location;
    }

    public function getRefereeId(): ?Uuid{
        return $this->refereeId;
    }

    public function setRefereeId(Uuid $refereeId): void{
        $this->refereeId = $refereeId;
    }
}