<?php

namespace App\ServiceFE;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use PDO;
use PDOException;
use RuntimeException;
use App\Entity\CatchRecord;
use App\Database\Database;
class CatchModel {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function deleteCatch(UuidInterface $catchId): void {
        try {
            $sql = "DELETE FROM catches WHERE id = :catchId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':catchId', $catchId->toString());
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Error deleting catch: " . $e->getMessage());
        }
    }

    public function addCatch(array $catchData): CatchRecord {
        $catch = $this->hydrateCatch($catchData);

        $sql = "INSERT INTO catches (id, species, length, competitor) 
                VALUES (:id, :species, :points, :competitor)";
        $statement = $this->pdo->prepare($sql);

        $result = $statement->execute([
            ':id' => $catch->getId()->toString(),
            ':species' => $catch->getSpecies(),
            ':points' => $catch->getPoints(),
            ':competitor' => $catch->getCompetitor()->toString()
        ]);

        if (!$result) {
            throw new RuntimeException('Failed to insert catch.');
        }

        return $catch;
    }

    public function catchExists(UuidInterface $catchId): bool {
        try {
            $sql = "SELECT COUNT(*) FROM catches WHERE id = :catchId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':catchId', $catchId->toString());
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking catch existence: " . $e->getMessage());
            return false;
        }
    }

    public function hydrateCatch(array $catchData): CatchRecord {
        $catch = new CatchRecord();

        $catch->setId(
            isset($catchData['id']) ? Uuid::fromString($catchData['id']) : Uuid::uuid4()
        );
        $catch->setCompetitor(Uuid::fromString($catchData['competitor']));
        $catch->setSpecies($catchData['species']);
        $catch->setPoints($catchData['length']);

        return $catch;
    }
}