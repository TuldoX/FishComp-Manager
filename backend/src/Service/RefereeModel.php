<?php

namespace App\Service;

use App\Entity\Competitor;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;

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
                throw new \RuntimeException('Critical database configuration is missing. Please set the required environment variables.');
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
            throw new \RuntimeException('Failed to connect to the database.');
        }
    }

    public function getCompetitors(UuidInterface $refereeId): array {
        try {
            $sql = "SELECT id, first_name, last_name, location FROM competitors WHERE referee = :refereeId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':refereeId', $refereeId->toString(), PDO::PARAM_STR);
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
            $statement->bindValue(':refereeId', $refereeId->toString(), PDO::PARAM_STR);
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

        return $competitor;
    }
}