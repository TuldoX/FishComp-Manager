<?php
namespace App\Service;

use App\Entity\Species;
use PDOException;
use PDO;
use RuntimeException;

class SpeciesModel{
    private PDO $pdo;

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
}