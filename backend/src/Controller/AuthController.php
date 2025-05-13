<?php
namespace App\Controller;

use App\View\JsonView;
use App\Service\AuthModel;
use App\Entity\JwtHelper;
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
            // Debug: Log request start
            error_log('Starting refereeLogin');

            // Initialize JWT
            $jwtSecret = getenv('JWT_SECRET_KEY');
            if (!$jwtSecret) {
                throw new Exception('JWT secret key not configured');
            }
            JwtHelper::initialize($jwtSecret);

            // Get input
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON input');
            }

            // Validate input
            $code = $input['code'] ?? '';
            $name = $input['name'] ?? '';

            if (!$this->validateCredentials($code, $name)) {
                return;
            }

            // Authenticate
            error_log("Attempting authentication for: $name");
            $referee = $this->authModel->refereeLogin($code, $name);

            if (!$referee) {
                $this->view->render(['error' => 'Invalid credentials'], 401);
                return;
            }

            // Generate token
            $token = $this->generateAuthToken($referee);
            $this->sendSuccessResponse($token, $referee);

        } catch (Exception $e) {
            error_log('Auth Error: ' . $e->getMessage());
            $this->view->render(['error' => 'Authentication service unavailable', 'debug' => $e->getMessage()], 500);
        }
    }

    public function testEndpoint(): void {
        try {
            $results = [
                'database' => $this->testDatabase(),
                'jwt' => $this->testJwt(),
                'environment' => $this->checkEnvironment()
            ];

            $this->view->render($results);
        } catch (Exception $e) {
            $this->view->render([
                'error' => 'Diagnostic failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function testDatabase(): array {
        try {
            $testModel = new AuthModel();
            $testQuery = $testModel->testConnection();
            return ['status' => 'connected', 'test_query' => $testQuery];
        } catch (Exception $e) {
            error_log('DB Test Failed: ' . $e->getMessage());
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function testJwt(): array {
        try {
            $secret = getenv('JWT_SECRET_KEY');
            if (!$secret) {
                throw new Exception('JWT_SECRET_KEY not set');
            }

            JwtHelper::initialize($secret);
            $token = JwtHelper::generateToken(['test' => true]);
            $decoded = JwtHelper::decodeToken($token);

            return [
                'status' => 'working',
                'token_sample' => $token,
                'decoded' => $decoded ? 'success' : 'failed'
            ];
        } catch (Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    private function checkEnvironment(): array {
        return [
            'POSTGRES_HOST' => getenv('POSTGRES_HOST') ?: 'not_set',
            'POSTGRES_DB' => getenv('POSTGRES_DB') ?: 'not_set',
            'JWT_SECRET_SET' => getenv('JWT_SECRET_KEY') ? 'yes' : 'no'
        ];
    }

    private function validateCredentials(string $code, string $name): bool {
        if (empty($code) || !preg_match('/^[a-zA-Z0-9]+$/', $code)) {
            $this->view->render(['error' => 'Invalid or missing code'], 400);
            return false;
        }

        if (empty($name) || !preg_match('/^[a-z]+\.[a-z]+$/', $name)) {
            $this->view->render(['error' => 'Invalid name format'], 400);
            return false;
        }

        return true;
    }

    private function generateAuthToken(Referee $referee): string {
        return JwtHelper::generateToken([
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