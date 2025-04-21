<?php

namespace App\Controller;

use App\Service\RefereeModel;
use App\View\JsonView;
use Ramsey\Uuid\Uuid;

class RefereeController {

    public function getCompetitors(string $refereeId): void {
        $refereeModel = new RefereeModel();
        $view = new JsonView();

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