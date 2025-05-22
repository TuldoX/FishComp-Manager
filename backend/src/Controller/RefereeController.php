<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\RefereeModel;
use App\View\JsonView;
use Ramsey\Uuid\Uuid;

class RefereeController {

    public function getCompetitors(string $refereeId): void {
        $refereeModel = new RefereeModel();
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

        // Second validation - just to make sure
        if (!Uuid::isValid($refereeId)) {
            $view->render(['error' => 'Invalid UUID format.'], 400);
            return;
        }

        // Check if referee exists
        if (!$refereeModel->refereeExists(Uuid::fromString($refereeId))) {
            $view->render(['error' => 'Referee not found.'], 404);
            return;
        }

        // Fetch competitors
        $data = $refereeModel->getCompetitors(Uuid::fromString($refereeId));

        // Handle empty results
        if (empty($data)) {
            $view->render(['message' => 'No competitors found for this referee.'], 204);
            return;
        }

        $view->render($data);
    }
}