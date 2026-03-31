<?php
class Activities
{
   private $conn;
   private $table = "activities";

   public $id;
   public $title;
   public $description;
   public $activity_type;
   public $status;
   public $activity_date;
   public $activity_time;
   public $location_id;
   public $created_by;

   public function __construct($database = null)
   {
      if ($database === null) {
         $database = new Database();
      }

      $this->conn = $database->connect();
   }

   public function getAllActivities($limit = 100, $offset = 0)
   {
      $query =
         "SELECT id, title, description, activity_type, status, activity_time, location_id, created_by FROM " .
         $this->table .
         " ORDER BY activity_time ASC LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function getActivityById($id)
   {
      $query = "SELECT id, title, description, activity_type, status, activity_time, location_id, created_by FROM " . $this->table . " WHERE id = :id LIMIT 1";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   public function createActivity($title, $description, $activity_type, $status, $activity_time, $location_id, $created_by)
   {
      $query =
         "INSERT INTO " .
         $this->table .
         " (title, description, activity_type, status, activity_time, location_id, created_by) VALUES (:title, :description, :activity_type, :status, :activity_time, :location_id, :created_by)";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(":title", $title);
      $stmt->bindValue(":description", $description);
      $stmt->bindValue(":activity_type", $activity_type);
      $stmt->bindValue(":status", $status);
      $stmt->bindValue(":activity_time", $activity_time);
      $stmt->bindValue(":location_id", $location_id);
      $stmt->bindValue(":created_by", $created_by, PDO::PARAM_INT);
      if ($stmt->execute()) {
         return (int) $this->conn->lastInsertId();
      }
      return false;
   }

   public function updateActivity($id, $title, $description, $activity_type, $status, $activity_time, $location_id)
   {
      $query =
         "UPDATE " .
         $this->table .
         " SET title = :title, description = :description, activity_type = :activity_type, status = :status, activity_time = :activity_time, location_id = :location_id WHERE id = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(":title", $title);
      $stmt->bindValue(":description", $description);
      $stmt->bindValue(":activity_type", $activity_type);
      $stmt->bindValue(":status", $status);
      $stmt->bindValue(":activity_time", $activity_time);
      $stmt->bindValue(":location_id", $location_id);
      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      return $stmt->execute();
   }

   public function deleteActivity($id, $requestUserId, $requestUserRole = "user")
   {
      if ($requestUserRole === "admin") {
         $query = "DELETE FROM " . $this->table . " WHERE id = :id";
         $stmt = $this->conn->prepare($query);
         $stmt->bindParam(":id", $id, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->rowCount() > 0;
      }

      $query = "DELETE FROM " . $this->table . " WHERE id = :id AND created_by = :request_user_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":id", $id, PDO::PARAM_INT);
      $stmt->bindParam(":request_user_id", $requestUserId, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->rowCount() > 0;
   }

   public function deleteActivitiesByUserId($user_id, $requestUserId, $requestUserRole = "user")
   {
      if ($requestUserRole !== "admin" && (int) $user_id !== (int) $requestUserId) {
         return false;
      }

      $query = "DELETE FROM " . $this->table . " WHERE created_by = :user_id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
      return $stmt->execute();
   }
}
