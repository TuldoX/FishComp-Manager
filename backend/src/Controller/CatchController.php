<?php
namespace App\Controller;

use Exception;
use Ramsey\Uuid\Uuid;
use App\View\JsonView;
use App\Service\CatchModel;
use App\Service\SpeciesModel;

class CatchController {
    public function deleteCatch(string $catchId): void {
        $catchModel = new CatchModel();
        $view = new JsonView();

        if (!Uuid::isValid($catchId)) {
            $view->render(['error' => 'Invalid UUID format.'], 400);
            return;
        }

        if (!$catchModel->catchExists(Uuid::fromString($catchId))) {
            $view->render(['error' => 'Catch not found.'], 404);
            return;
        }
        try {
            $catchModel->deleteCatch(Uuid::fromString($catchId));
            $view->render(['success' => 'Catch deleted successfully.']);
        } catch (Exception $e) {
            $view->render(['error' => $e->getMessage()], 500);
        }
    }

    public function createCatch(): void {
        $catchModel = new CatchModel();
        $speciesModel = new SpeciesModel();
        $view = new JsonView();

        $body = file_get_contents('php://input');
        $bodyData = json_decode($body, true);

        // Check for valid JSON
        if (empty($bodyData)) {
            $view->render(['error' => 'Invalid JSON in request body.'], 400);
            return;
        }

        // Check if all required fields are present
        $requiredFields = ['competitor', 'referee', 'length', 'species'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (!isset($bodyData[$field])) {
                $errors[] = "Missing required field: $field";
            }
        }

        if (!empty($errors)) {
            $view->render(['errors' => $errors], 400);
            return;
        }

        // Validate UUID fields
        if (!Uuid::isValid($bodyData['competitor'])) {
            $errors[] = 'Invalid competitor UUID format';
        }

        // Validate length field
        if (!is_numeric($bodyData['length']) || $bodyData['length'] <= 0) {
            $errors[] = 'Length must be a positive number';
        }

        // Validate species field
        if (!is_int($bodyData['species']) && !ctype_digit($bodyData['species'])) {
            $errors[] = 'Species must be a positive integer';
        } else {

            if ($bodyData['species'] <= 0) {
                $errors[] = 'Species must be a positive integer';
            }
        }

        // Validate length against max length for species
        if (empty($errors)) {
            $maxLength = $speciesModel->getMaxLengthBySpeciesId($bodyData['species']);
            if ($maxLength === null) {
                $errors[] = 'Species not found.';
            } elseif ($bodyData['length'] > $maxLength) {
                $errors[] = "Length exceeds the maximum allowed length of $maxLength cm for this species.";
            }
        }

        if (!empty($errors)) {
            $view->render(['errors' => $errors], 400);
            return;
        }

        try {
            // Convert UUID strings to UUID objects
            $bodyData['competitor'] = Uuid::fromString($bodyData['competitor']);
            $bodyData['referee'] = Uuid::fromString($bodyData['referee']);

            // Add catch
            $data = $catchModel->addCatch($bodyData);
            $view->render($data, 201);
        } catch (Exception $e) {
            $view->render(['error' => $e->getMessage()], 500);
        }
    }
}