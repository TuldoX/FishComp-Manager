<?php
namespace App\Entity;

use Ramsey\Uuid\Uuid;

class Referee{
    private Uuid $id;
    private string $code;

    public function getId(): ?Uuid{
        return $this->id;
    }

    public function getCode(): ?string{
        return $this->code;
    }

    // TODO: Pri dorobení adminovskej stránky dorobiť aj metódy
}