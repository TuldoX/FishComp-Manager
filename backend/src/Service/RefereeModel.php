<?php

namespace App\Service;

use App\Entity\Competitor;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;
use RuntimeException;

class RefereeModel {

    private PDO $pdo;

    // Database connection when creating RefereeModel object
    public function __construct() {
        try {
            $host = getenv('POSTGRES_HOST') ?: 'default_database';
            $port = getenv('POSTGRES_PORT') ?: 5432;
            $dbname = getenv('POSTGRES_DB') ?: 'postgres';
            $user = getenv('POSTGRES_USER') ?: 'default_user';
            $password = getenv('POSTGRES_PASSWORD') ?: 'default_password';

            if ($host === 'default_database' || $user === 'default_user') {
                throw new RuntimeException('Critical database configuration is missing. Please set the required environment variables.');
            }

            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname);

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            // Log the error and rethrow it
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Failed to connect to the database.');
        }
    }

    public function getCompetitors(UuidInterface $refereeId): array {
        try {
            $sql = "SELECT 
                    c.id,
                    c.first_name,
                    c.last_name,
                    c.location,
                (
                    SELECT COALESCE(SUM(length), 0)
                    FROM catches
                    WHERE catches.competitor= c.id
                ) AS total_points
                FROM competitors c
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
            // Log error and return an empty array
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
            // Log error and return false
            error_log($e->getMessage());
            return false;
        }
    }

    public function hydrate(array $competitorData): Competitor {
        $competitor = new Competitor();

        $competitor->setId(Uuid::fromString($competitorData['id']));
        $competitor->setFirstName($competitorData['first_name']);
        $competitor->setLastName($competitorData['last_name']);
        $competitor->setLocation($competitorData['location']);
        $competitor->setPoints($competitorData['total_points']);

        return $competitor;
    }
}