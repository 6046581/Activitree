<?php

class User {

    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getUserById(int $id): ?array {

        $stmt = $this->conn->prepare(
            "SELECT username FROM users WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }
}