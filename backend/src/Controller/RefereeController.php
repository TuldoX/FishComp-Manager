<?php

namespace App\Controller;

use App\Entity\Referee;
use App\Service\RefereeModel;
use App\View\JsonView;

class RefereeController{
    public function read() : void {
        $refereeModel = new RefereeModel();
        $data = $refereeModel->getCompetitors();

        $view = new JsonView();
        $view->render($data,201);
    }
}