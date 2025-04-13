<?php
namespace App\Service;

use App\Entity\Referee;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;

class AuthModel {
    private PDO $pdo;

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

    public function refereeLogin(string $code): ?Referee {
        try {
            $sql = "SELECT id, first_name, last_name FROM referees WHERE code = :code";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $this->hydrate($row); // Convert the row to a Referee object
            }

            return null; // Return null if no row is found
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function refereeExists(string $code): bool {
        try {
            $sql = "SELECT COUNT(*) FROM referees WHERE code = :code";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log error and return false
            error_log($e->getMessage());
            return false;
        }
    }

    public function hydrate(array $refereeData): Referee {
        $referee = new Referee();

        $referee->setId(Uuid::fromString($refereeData['id']));
        $referee->setFirstName($refereeData['first_name']);
        $referee->setLastName($refereeData['last_name']);

        return $referee;
    }
}