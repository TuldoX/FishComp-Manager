<?php
namespace App\Controller;

use App\Service\CompetitorModel;
use App\View\JsonView;
use Ramsey\Uuid\Uuid;
use App\Service\AuthService;

class CompetitorController{
    public function getCatches(string $competitorId): void{
        $competitorModel = new CompetitorModel();
        $view = new JsonView();

        //JWT check and validation
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
        if (!Uuid::isValid($competitorId)) {
            $view->render(['error' => 'Invalid UUID format.'], 400);
            return;
        }

        //Check if competitor exists
        if (!$competitorModel->competitorExists(Uuid::fromString($competitorId))) {
            $view->render(['error' => 'Competitor not found.'], 404);
            return;
        }

        //Fetch catches
        $data = $competitorModel->getCatches(Uuid::fromString($competitorId));

        // Handle empty results
        if (empty($data)) {
            $view->render(['message' => 'No cathes found for this competitor.'], 204);
            return;
        }

        $view->render($data);
    }
}

