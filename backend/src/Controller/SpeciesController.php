<?php
namespace App\Controller;

use App\Service\AuthService;
use App\View\JsonView;
use App\Service\SpeciesModel;

class SpeciesController{
    public function getSpecies() : void {
        $speciesModel = new SpeciesModel();
        $view = new JsonView();

        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        $authService = new AuthService();

        // $jwtSecret = getenv('JWT_SECRET_KEY');
        // if (!$jwtSecret) {
        //     throw new Exception('JWT secret key not configured');
        // }
        // AuthService::initialize($jwtSecret);

        if(!$authService::isValidToken($authHeader)){
            $view->render(['error' => 'Unauthorized'],401);
            return;
        }

        $data = $speciesModel->getSpecies();

        if(empty($data)){
            $view->render(['message' => 'No species found.'], 204);
        }

        $view->render($data);
    }
}