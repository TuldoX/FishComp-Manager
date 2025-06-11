<?php
namespace App\ServiceFE;

use App\Entity\CatchRecord;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;
use App\Database\Database;

class CompetitorModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getCatches(UuidInterface $competitorId): array
    {
        try {
            $sql = "
                SELECT c.id, s.name, c.length
                FROM catches c
                JOIN species s ON s.id = c.species
                WHERE c.competitor = :competitorId
                ORDER BY c.length DESC;
            ";

            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':competitorId', $competitorId->toString());
            $statement->execute();

            $rows = $statement->fetchAll();

            $catches = [];
            foreach ($rows as $row) {
                $catches[] = $this->hydrateCatch($row);
            }

            return $catches;
        } catch (PDOException $e) {
            error_log('Error fetching catches: ' . $e->getMessage());
            return [];
        }
    }

    public function competitorExists(UuidInterface $competitorId): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM competitors WHERE id = :competitorId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':competitorId', $competitorId->toString());
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('Error checking competitor existence: ' . $e->getMessage());
            return false;
        }
    }

    private function hydrateCatch(array $row): CatchRecord
    {
        $catch = new CatchRecord();

        $catch->setId(Uuid::fromString($row['id']));
        $catch->setSpecies($row['species']);
        $catch->setPoints($row['points']);

        return $catch;
    }
}