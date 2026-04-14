<?php
class Locations
{
   private $conn;
   private $table = "locations";

   public function __construct($database = null)
   {
      if ($database === null) {
         $database = Database::getInstance();
      }

      $this->conn = $database->getConnection();
   }

   public function getAllLocations($limit = 200, $offset = 0)
   {
      $query =
         "SELECT id, latitude, longitude, country, country_code, city, postal_code, street, house_number, formatted_address FROM " .
         $this->table .
         " ORDER BY id ASC LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getLocationById($id)
   {
      $query =
         "SELECT id, latitude, longitude, country, country_code, city, postal_code, street, house_number, formatted_address FROM " .
         $this->table .
         " WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC);
   }
}
