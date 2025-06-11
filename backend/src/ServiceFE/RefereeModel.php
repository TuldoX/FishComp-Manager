<?php

namespace App\ServiceFE;

use App\Entity\Competitor;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;
use App\Database\Database;

class RefereeModel {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getCompetitors(UuidInterface $refereeId): array {
        try {
            $sql = "
                SELECT
                    c.id,
                    c.first_name,
                    c.last_name,
                    c.location,
                COALESCE(S.total_points, 0) AS total_points
                FROM competitors c
                LEFT JOIN (
                    SELECT competitor, SUM(length) AS total_points
                    FROM catches
                    GROUP BY competitor
                ) S ON S.competitor = c.id
                WHERE c.referee = :refereeId;
            ";

            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':refereeId', $refereeId->toString());
            $statement->execute();

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $competitors = [];
            foreach ($rows as $row) {
                $competitors[] = $this->hydrate($row);
            }

            return $competitors;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function refereeExists(UuidInterface $refereeId): bool {
        try {
            $sql = "SELECT COUNT(*) FROM referees WHERE id = :refereeId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':refereeId', $refereeId->toString());
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrate(array $competitorData): Competitor {
        $competitor = new Competitor();

        $competitor->setId(Uuid::fromString($competitorData['id']));
        $competitor->setFirstName($competitorData['first_name']);
        $competitor->setLastName($competitorData['last_name']);
        $competitor->setLocation($competitorData['location']);
        $competitor->setPoints($competitorData['total_points']);

        return $competitor;
    }
}