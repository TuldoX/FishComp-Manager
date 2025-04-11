<?php

namespace App\Service;

use App\Entity\Competitor;
use Ramsey\Uuid\Uuid;

class RefereeModel{
    public function getCompetitors() : array{
        $competitors = [];
        $uuid = Uuid::fromString('6c39ff74-2d4e-459c-b7d2-54f569bbdb83');
        
        for ($i = 0; $i < 10; $i++) {
            $competitors[] = new Competitor($uuid, 'John Doe',20,$uuid);
        }

        return $competitors;
    }
}