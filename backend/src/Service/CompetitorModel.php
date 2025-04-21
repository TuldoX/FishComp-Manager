<?php
namespace App\Service;

use App\Entity\CatchRecord;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;
use RuntimeException;

class CompetitorModel
{
    private PDO $pdo;

    // Database connection when creating RefereeModel object
    public function __construct()
    {
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
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            // Log the error and rethrow it
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Failed to connect to the database.');
        }
    }

    public function getCatches(UuidInterface $competitorId): array
    {
        try {
            $sql = "SELECT 
                    c.id,
                    (
                        SELECT name 
                        FROM species
                        WHERE species.id = c.species
                    ) AS species,
                    c.length as points
                FROM catches c
                WHERE c.competitor = :competitorId;
                ";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':competitorId', $competitorId->toString());
            $statement->execute();

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $catches = [];
            foreach ($rows as $row) {
                $catches[] = $this->hydrateCatch($row);
            }

            return $catches;
        } catch (PDOException $e) {
            // Log error and return an empty array
            error_log($e->getMessage());
            return [];
        }
    }

    public function competitorExists(UuidInterface $competitorId): bool {
        try {
            $sql = "SELECT COUNT(*) FROM competitors WHERE id = :competitorId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':competitorId', $competitorId->toString());
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log error and return false
            error_log($e->getMessage());
            return false;
        }
    }

    private function hydrateCatch(array $row): CatchRecord{
        $catch  = new CatchRecord();

        $catch->setId(Uuid::fromString($row['id']));
        $catch->setSpecies($row['species']);
        $catch->setPoints($row['points']);

        return $catch;
    }
}