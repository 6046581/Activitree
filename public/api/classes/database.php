<?php

class Database {

    private $conn;

    public function __construct() {
        $dbName = getenv("DB_NAME") ?: "activitree";
        $dbPort = getenv("DB_PORT") ?: "3306";

        $hostFromEnv = getenv("DB_HOST") ?: null;
        $userFromEnv = getenv("DB_USER") ?: null;
        $passFromEnv = getenv("DB_PASSWORD") ?: null;

        $attempts = [];
        $portsToTry = array_values(array_unique(array_filter([
            $dbPort,
            "3306",
            "3307",
            "33060"
        ])));
        $hostsToTry = array_values(array_unique(array_filter([
            $hostFromEnv,
            "localhost",
            "127.0.0.1",
            "mysql",
            "db",
            "database",
            "mariadb",
            "host.docker.internal"
        ])));

        if ($hostFromEnv !== null && $userFromEnv !== null) {
            foreach ($portsToTry as $port) {
                $attempts[] = [
                    "host" => $hostFromEnv,
                    "port" => $port,
                    "user" => $userFromEnv,
                    "pass" => $passFromEnv ?: "",
                ];
            }
        }

        foreach ($hostsToTry as $host) {
            foreach ($portsToTry as $port) {
                $attempts[] = ["host" => $host, "port" => $port, "user" => "root", "pass" => "root"];
                $attempts[] = ["host" => $host, "port" => $port, "user" => "root", "pass" => ""];
            }
        }

        $errors = [];

        foreach ($attempts as $attempt) {
            try {
                $dsn = "mysql:host={$attempt['host']};port={$attempt['port']};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $attempt["user"], $attempt["pass"], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $this->conn = $pdo;
                return;
            } catch (Throwable $exception) {
                $errors[] = $attempt["host"] . ":" . $attempt["port"] . "/" . $attempt["user"] . ": " . $exception->getMessage();
            }
        }

        throw new RuntimeException("Database connection failed. Attempts: " . implode(" | ", $errors));
    }

    public function getConnection() {
        return $this->conn;
    }
}