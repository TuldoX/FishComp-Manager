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

    public function createCatch(): void {
        $catchModel = new CatchModel();
        $view = new JsonView();

        $body = file_get_contents('php://input');
        $bodyData = json_decode($body, true);

        // Check for valid JSON
        if (empty($bodyData)) {
            $view->render(['error' => 'Invalid JSON in request body.'], 400);
            return; // Missing return statement in original
        }

        // Check if all required fields are present
        $requiredFields = ['competitor', 'referee', 'length', 'species'];
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($bodyData[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        if (!empty($errors)) {
            $view->render(['errors' => $errors], 400);
            return;
        }

        // Validate each field
        try {
            // UUID validations
            if (!Uuid::isValid($bodyData['competitor'])) {
                $errors[] = 'Invalid competitor UUID format';
            }
            
            if (!Uuid::isValid($bodyData['referee'])) {
                $errors[] = 'Invalid referee UUID format';
            }

            // Length validation
            if (!is_numeric($bodyData['length']) || $bodyData['length'] <= 0) {
                $errors[] = 'Length must be a positive number';
            }

            // Species validation
            if (!is_int($bodyData['species']) || $bodyData['species'] <= 0) {
                $errors[] = 'Species must be a positive integer';
            }

            if (!empty($errors)) {
                $view->render(['errors' => $errors], 400);
                return;
            }

            // Convert string UUIDs to UUID objects before passing to model
            $bodyData['competitor'] = Uuid::fromString($bodyData['competitor']);
            $bodyData['referee'] = Uuid::fromString($bodyData['referee']);
            
            $data = $catchModel->addCatch($bodyData);
            $view->render($data,201);

        } catch (Exception $e) {
            $view->render(['error' => $e->getMessage()], 500);
        }
    }
}