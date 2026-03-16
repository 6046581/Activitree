<?php

class User {

    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getUserById(int $id): ?array {

        $stmt = $this->conn->prepare(
            "SELECT id, username FROM users WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    public function outputUserByIdJson(?int $id): void {
        if ($id === null || $id <= 0) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Missing or invalid user id"
            ]);
            return;
        }

        $user = $this->getUserById($id);

        if ($user === null) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "User not found"
            ]);
            return;
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "user" => $user
        ]);
    }
}