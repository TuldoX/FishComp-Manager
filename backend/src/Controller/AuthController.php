<?php
namespace App\Controller;

use App\Entity\Referee;
use App\Service\AuthModel;
use App\Service\AuthService;
use App\View\JsonView;
use Exception;

class AuthController {
    private JsonView $view;
    private AuthModel $authModel;

    public function __construct() {
        $this->view = new JsonView();
        $this->authModel = new AuthModel();
    }

    public function refereeLogin(): void {
        try {
            $jwtSecret = getenv('JWT_SECRET_KEY');
            if (!$jwtSecret) {
                throw new Exception('JWT secret key not configured');
            }
            AuthService::initialize($jwtSecret);

            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON input');
            }

            $code = $input['code'] ?? '';
            $name = $input['name'] ?? '';

            if (!$this->validateCredentials($code, $name)) {
                return;
            }

            $referee = $this->authModel->refereeLogin($code, $name);

            if (!$referee) {
                $this->view->render(['message' => 'Neplatné údaje'], 401);
                return;
            }

            $token = $this->generateAuthToken($referee);
            $this->sendSuccessResponse($token, $referee);

        } catch (Exception) {
            $this->view->render(['message' => 'Authentication service unavailable'], 500);
        }
    }

    private function validateCredentials(string $code, string $name): bool {
        if (empty($code) || !preg_match('/^[a-zA-Z0-9]+$/', $code)) {
            $this->view->render(['message' => 'Neplatný kód'], 400);
            return false;
        }

        if (empty($name) || !preg_match('/^[a-z]+\.[a-z]+$/', $name)) {
            $this->view->render(['message' => 'Neplatný formát mena'], 400);
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function generateAuthToken(Referee $referee): string {
        return AuthService::generateToken([
            'id' => $referee->getId()->toString(),
            'firstName' => $referee->getFirstName(),
            'lastName' => $referee->getLastName(),
            'role' => 'referee'
        ]);
    }

    private function sendSuccessResponse(string $token, Referee $referee): void {
        $this->view->render([
            'token' => $token,
            'referee' => [
                'id' => $referee->getId()->toString(),
                'firstName' => $referee->getFirstName(),
                'lastName' => $referee->getLastName()
            ]
        ]);
    }
}