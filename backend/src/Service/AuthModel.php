<?php
namespace App\Service;

use App\Entity\Referee;
use Ramsey\Uuid\Uuid;
use PDO;
use PDOException;
use RuntimeException;

class AuthModel {
    private PDO $pdo;

    public function __construct() {
        $this->connect();
    }

    private function connect(): void {
        try {
            $host = getenv('POSTGRES_HOST') ?: 'localhost';
            $port = getenv('POSTGRES_PORT') ?: 5432;
            $dbname = getenv('POSTGRES_DB') ?: 'postgres';
            $user = getenv('POSTGRES_USER') ?: 'postgres';
            $password = getenv('POSTGRES_PASSWORD') ?: '';

            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            error_log("Connecting to: $dsn");

            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public function refereeLogin(string $code, string $name): ?Referee {
        try {
            error_log("Login attempt for: $name");
            $sql = "SELECT id, first_name, last_name, code FROM referees WHERE user_name = :name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch();
            if (!$row) {
                error_log("No user found for: $name");
                return null;
            }

            error_log("User found: " . json_encode($row));
            if (!password_verify($code, $row['code'])) {
                error_log("Password verification failed for: $name");
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            throw new RuntimeException("Login processing failed");
        }
    }

    public function testConnection(): array {
        try {
            $stmt = $this->pdo->query("SELECT 1 as test_value");
            $result = $stmt->fetch();
            return ['success' => true, 'test_value' => $result['test_value']];
        } catch (PDOException $e) {
            error_log("Test query failed: " . $e->getMessage());
            throw new RuntimeException("Test query failed");
        }
    }

    private function hydrate(array $data): Referee {
        $referee = new Referee();
        $referee->setId(Uuid::fromString($data['id']));
        $referee->setFirstName($data['first_name']);
        $referee->setLastName($data['last_name']);
        return $referee;
    }
}