<?php

class Database {
    private $host = "127.0.0.1";
    private $port = "3306";
    private $dbname = "activitree";
    private $username = "root";
    private $password = "root";
    private $conn;

    public function connect() {
        $this->conn = null;

        $host = getenv('DB_HOST') ?: $this->host;
        $port = getenv('DB_PORT') ?: $this->port;
        $dbname = getenv('DB_NAME') ?: $this->dbname;
        $username = getenv('DB_USER') ?: $this->username;
        $password = getenv('DB_PASS') ?: $this->password;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $dbname . ";charset=utf8mb4",
                $username,
                $password
            );

            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }
}