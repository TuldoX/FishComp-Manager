<?php
namespace App\Controller;

use App\View\JsonView;
use App\Service\CompetitorModel;
use Ramsey\Uuid\Uuid;

class CompetitorController{
    public function getCatches(string $competitorId): void{
        $competitorModel = new CompetitorModel();
        $view = new JsonView();

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

        $view->render(['data' => $data]);
    }
}

