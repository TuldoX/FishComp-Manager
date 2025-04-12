<?php

namespace App\Service;

use App\Entity\Competitor;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use PDO;

class RefereeModel {

    private PDO $pdo;

    public function __construct() {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('POSTGRES_HOST') ?: 'db',
            getenv('POSTGRES_PORT') ?: 5432,
            getenv('POSTGRES_DB') ?: 'postgres'
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dbUser = getenv('POSTGRES_USER') ?? 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?? 'default_password';

        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

    public function getCompetitors(UuidInterface $refereeId): array {
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
    }

    //TODO: check if referee exists

    public function hydrate(array $competitorData): Competitor {
        $competitor = new Competitor();

        $competitor->setId(Uuid::fromString($competitorData['id']));
        $competitor->setFirstName($competitorData['first_name']);
        $competitor->setLastName($competitorData['last_name']);
        $competitor->setLocation($competitorData['location']);

        return $competitor;
    }
}