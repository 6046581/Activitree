<?php

class Database
{
   private $conn;

   public function connect()
   {
      $this->conn = null;

      $host = getenv("DB_HOST") ?: "db";
      $port = getenv("DB_PORT") ?: "3306";
      $dbname = getenv("DB_NAME") ?: "activitree";
      $username = getenv("DB_USER") ?: "root";
      $password = getenv("DB_PASS") ?: "root";

      try {
         $this->conn = new PDO("mysql:host=" . $host . ";port=" . $port . ";dbname=" . $dbname . ";charset=utf8mb4", $username, $password);

         $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
         $this->conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
         throw new RuntimeException("Database connection failed: " . $e->getMessage());
      }

      return $this->conn;
   }
}
