<?php
namespace App\Controller;

use App\View\JsonView;
use App\Service\SpeciesModel;

class SpeciesController{
    public function getSpecies() : void {
        $speciesModel = new SpeciesModel();
        $view = new JsonView();

        $data = $speciesModel->getSpecies();

        if(empty($data)){
            $view->render(['message' => 'No species found.'], 204);
        }

        $view->render($data);
    }
}