<?php
namespace App\Service;

use Ramsey\Uuid\UuidInterface;
use PDO;
use PDOException;
use RuntimeException;
class CatchModel{

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

    public function deleteCatch(UuidInterface $catchId) : void{
        try {
            $sql = "DELETE FROM catches WHERE id = :catchId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':catchId', $catchId->toString());
            $statement->execute();
        } catch (PDOException $e) {
            // Log error
            error_log($e->getMessage());
        }
    }
    public function catchExists(UuidInterface $catchId): bool{
        try {
            $sql = "SELECT COUNT(*) FROM catches WHERE id = :catchId";
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':catchId', $catchId->toString());
            $statement->execute();

            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log error and return false
            error_log($e->getMessage());
            return false;
        }
    }
}