<?php
namespace App\ServiceFE;

use App\Entity\Referee;
use Ramsey\Uuid\Uuid;
use PDO;
use PDOException;
use RuntimeException;
use App\Database\Database;

class AuthModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function refereeLogin(string $code, string $name): ?Referee {
        try {
            $sql = "SELECT id, first_name, last_name, code FROM referees WHERE user_name = :name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name);
            $stmt->execute();

            $row = $stmt->fetch();
            if (!$row || !password_verify($code, $row['code'])) {
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            throw new RuntimeException("Login processing failed.");
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