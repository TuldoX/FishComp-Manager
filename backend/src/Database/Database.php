<?php

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            try {
                $host = getenv('POSTGRES_HOST') ?: 'default_database';
                $port = getenv('POSTGRES_PORT') ?: 5432;
                $dbname = getenv('POSTGRES_DB') ?: 'postgres';
                $user = getenv('POSTGRES_USER') ?: 'default_user';
                $password = getenv('POSTGRES_PASSWORD') ?: 'default_password';

                if ($host === 'default_database' || $user === 'default_user') {
                    throw new RuntimeException('Missing DB configuration.');
                }

                $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname);
                self::$pdo = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_PERSISTENT         => true, // ✅ Persistent connection
                ]);
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                throw new RuntimeException('Failed to connect to database.');
            }
        }

        return self::$pdo;
    }
}
