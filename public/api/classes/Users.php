<?php
class Users extends AbstractModel
{
   private $table = "users";

   private function hashToken($token)
   {
      return hash("sha256", (string) $token);
   }

   public $id;
   public $username;
   public $email;
   public $password;

   public function __construct($database = null)
   {
      parent::__construct($database);
   }

   public function loginUser($email, $password)
   {
      $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(":email", $email);

      $stmt->execute();

      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user["password"])) {
         unset($user["password"]);
         return $user;
      }
      return false;
   }

   public function signupUser($username, $email, $password)
   {
      $query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)";
      $stmt = $this->conn->prepare($query);

      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt->bindValue(":username", $username);
      $stmt->bindValue(":email", $email);
      $stmt->bindValue(":password", $hash);

      if ($stmt->execute()) {
         return (int) $this->conn->lastInsertId();
      }

      return false;
   }

   public function usernameExists($username)
   {
      $query = "SELECT 1 FROM " . $this->table . " WHERE username = :username LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":username", $username);
      $stmt->execute();

      return (bool) $stmt->fetchColumn();
   }

   public function emailExists($email)
   {
      $query = "SELECT 1 FROM " . $this->table . " WHERE email = :email LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":email", $email);
      $stmt->execute();

      return (bool) $stmt->fetchColumn();
   }

   public function getUserById($id)
   {
      $query = "SELECT id, username, email, avatar_path AS avatar_path FROM " . $this->table . " WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(":id", $id, PDO::PARAM_INT);

      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function getAllUsers($limit = 100, $offset = 0)
   {
      $query = "SELECT id, username, created_at, avatar_path AS avatar_path FROM " . $this->table . " ORDER BY id ASC LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);

      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function updateUser($id, $username, $email)
   {
      $query = "UPDATE " . $this->table . " SET username = :username, email = :email WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":username", $username);
      $stmt->bindParam(":email", $email);

      return $stmt->execute();
   }

   public function updateUserPassword($id, $password)
   {
      $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":password", $hash);

      return $stmt->execute();
   }

   public function updateAvatarPath($id, $avatarPath)
   {
      $query = "UPDATE " . $this->table . " SET avatar_path = :avatar_path WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->bindValue(":avatar_path", $avatarPath);

      return $stmt->execute();
   }

   public function setToken($id, $token)
   {
      $query = "UPDATE " . $this->table . " SET token = :token WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $tokenToStore = $token === null ? null : $this->hashToken($token);
      $stmt->bindValue(":token", $tokenToStore);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);

      return $stmt->execute();
   }

   public function getUserByToken($token)
   {
      if (!$token) {
         return null;
      }

      $tokenHash = $this->hashToken($token);

      $query = "SELECT id, username, email, role, avatar_path AS avatar_path FROM " . $this->table . " WHERE token = :token LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":token", $tokenHash);

      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function deleteUser($id)
   {
      $query = "DELETE FROM " . $this->table . " WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $stmt->bindParam(":id", $id, PDO::PARAM_INT);

      return $stmt->execute();
   }
}
