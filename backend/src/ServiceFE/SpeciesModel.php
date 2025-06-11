<?php
namespace App\ServiceFE;

use App\Entity\Species;
use PDOException;
use PDO;
use App\Database\Database;

class SpeciesModel{
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getSpecies() : array {
        try{
            $sql = "SELECT * FROM species ORDER BY id ";

            $statement = $this->pdo->prepare($sql);
            $statement->execute();

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $species = [];
            foreach ($rows as $row) {
                $species[] = $this->hydrateSpecies($row);
            }

            return $species;
        } catch (PDOException $e) {
            // Log error and return an empty array
            error_log($e->getMessage());
            return [];
        }
    }

    public function hydrateSpecies(array $row) : Species{
        $species = new Species();

        $species->setId($row['id']);
        $species->setName($row['name']);
        $species->setMaxlength($row['max_length']);

        return $species;
    }
    public function getMaxLengthBySpeciesId(int $speciesId): ?int {
        try {
            $sql = "SELECT max_length FROM species WHERE id = :speciesId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':speciesId', $speciesId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['max_length'])) {
                return (int)$result['max_length'];
            }
            return null; // Species not found
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null; // On error, treat as not found
        }
    }

}