<?php
namespace App\Controller;

use App\View\JsonView;
use App\Service\AuthModel;

class AuthController{
    public function refereeLogin(): void {
        $authModel = new AuthModel();
        $view = new JsonView();

        // Retrieve the raw input from the request body
        $input = json_decode(file_get_contents('php://input'), true);

        // Check if the JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            $view->render(['error' => 'Invalid JSON in request body.'], 400);
            return;
        }

        // Validate the presence of the "code" parameter
        $code = $input['code'] ?? null;
        $code = trim($code);
        if (!$code || !preg_match('/^[a-zA-Z0-9]+$/', $code)) {
            $view->render(['error' => 'Invalid or missing code.'], 400);
            return;
        }

        if (!$authModel->refereeExists($code)) {
            $view->render(['error' => 'Referee not found.'], 404);
            return;
        }

        try{
            $data = $authModel->refereeLogin($code);

            // Handle empty results
            if ($data === null) {
                $view->render(['message' => 'No referee found.'], 204);
                return;
            }

            $view->render($data);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $view->render(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}