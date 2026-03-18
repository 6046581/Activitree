<?php

class User {

    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->conn->prepare(
            "SELECT id, username, email, role, created_at FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function getUserByEmail(string $email): ?array {
        $stmt = $this->conn->prepare(
            "SELECT id, username, email, password, role, created_at FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->execute([$email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function toPublicUser(array $user): array {
        return [
            "id" => (int) $user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "created_at" => $user["created_at"]
        ];
    }

    public function authenticate(string $email, string $password): ?array {
        $user = $this->getUserByEmail($email);

        if ($user === null) {
            return null;
        }

        $storedPassword = (string) $user["password"];
        $isHashed = password_get_info($storedPassword)["algo"] !== null;
        $passwordMatches = $isHashed
            ? password_verify($password, $storedPassword)
            : hash_equals($storedPassword, $password);

        if (!$passwordMatches) {
            return null;
        }

        return $this->toPublicUser($user);
    }

    public function findPublicUserById(int $id): ?array {
        $user = $this->getUserById($id);

        if ($user === null) {
            return null;
        }

        return $this->toPublicUser($user);
    }
}