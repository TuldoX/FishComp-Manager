<?php
namespace App\Controller;

use Exception;
use Ramsey\Uuid\Uuid;
use App\View\JsonView;
use App\Service\CatchModel;

class CatchController{
    public function deleteCatch(string $catchId): void{
        $catchModel = new CatchModel();
        $view = new JsonView();

        if (!Uuid::isValid($catchId)) {
            $view->render(['error' => 'Invalid UUID format.'], 400);
            return;
        }

        if(!$catchModel->catchExists(Uuid::fromString($catchId))){
            $view->render(['error' => 'Catch not found.'], 404);
            return;
        }

        try{
            $catchModel->deleteCatch(Uuid::fromString($catchId));
            $view->render(['success' => 'Catch deleted successfully.']);
        } catch(Exception $e) {
            $view->render(['error' => $e->getMessage()],500);
        }

    }
}
