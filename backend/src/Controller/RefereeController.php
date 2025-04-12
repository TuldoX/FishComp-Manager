<?php

namespace App\Controller;

use App\Entity\Referee;
use App\Service\RefereeModel;
use App\View\JsonView;
use Ramsey\Uuid\Uuid;

class RefereeController {

    public function getCompetitors(string $refereeId): void {
        $refereeModel = new RefereeModel();

        $uuid = Uuid::fromString($refereeId);
        $data = $refereeModel->getCompetitors($uuid);

        $view = new JsonView();
        $view->render($data);
    }
}