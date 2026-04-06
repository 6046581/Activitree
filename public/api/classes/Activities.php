<?php
class Activities extends AbstractModel
{
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
      parent::__construct($database);
   }

   public function getAllActivities($limit = 100, $offset = 0)
   {
      $query =
         "SELECT a.id, a.title, a.description, a.activity_type, a.status, a.activity_time, a.location_id, a.photo_path, a.created_by, u.username AS created_by_username
          FROM " .
         $this->table .
         " a
          LEFT JOIN users u ON u.id = a.created_by
          ORDER BY a.activity_time ASC
          LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
      $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);

      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $this->attachParticipantIds($rows);
   }

   public function getActivityById($id)
   {
      $query =
         "SELECT a.id, a.title, a.description, a.activity_type, a.status, a.activity_time, a.location_id, a.photo_path, a.created_by, u.username AS created_by_username
             FROM " .
         $this->table .
         " a
             LEFT JOIN users u ON u.id = a.created_by
             WHERE a.id = :id
             LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
         return false;
      }

      $rows = $this->attachParticipantIds([$row]);
      return $rows[0] ?? false;
   }

   public function getActivityParticipants($activityId)
   {
      $query = "SELECT u.id, u.username, u.role, u.avatar_path, ap.role AS activity_role, ap.joined_at
          FROM activity_participants ap
          INNER JOIN users u ON u.id = ap.user_id
          WHERE ap.activity_id = :activity_id
          ORDER BY ap.joined_at ASC";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":activity_id", $activityId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   public function isUserParticipating($activityId, $userId)
   {
      $query = "SELECT 1 FROM activity_participants WHERE activity_id = :activity_id AND user_id = :user_id LIMIT 1";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":activity_id", $activityId, PDO::PARAM_INT);
      $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
      $stmt->execute();

      return (bool) $stmt->fetchColumn();
   }

   public function joinActivity($activityId, $userId, $role = "participant")
   {
      $query = "INSERT INTO activity_participants (activity_id, user_id, role) VALUES (:activity_id, :user_id, :role)";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":activity_id", $activityId, PDO::PARAM_INT);
      $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
      $stmt->bindValue(":role", $role);

      return $stmt->execute();
   }

   public function leaveActivity($activityId, $userId)
   {
      $query = "DELETE FROM activity_participants WHERE activity_id = :activity_id AND user_id = :user_id";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":activity_id", $activityId, PDO::PARAM_INT);
      $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->rowCount() > 0;
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

   public function updateActivityPhotoPath($id, $photoPath)
   {
      $query = "UPDATE " . $this->table . " SET photo_path = :photo_path WHERE id = :id";
      $stmt = $this->conn->prepare($query);

      $stmt->bindValue(":id", $id, PDO::PARAM_INT);
      $stmt->bindValue(":photo_path", $photoPath);

      return $stmt->execute();
   }

   private function attachParticipantIds(array $activities)
   {
      if (empty($activities)) {
         return $activities;
      }

      $activityIds = [];
      foreach ($activities as $activity) {
         $activityIds[] = (int) $activity["id"];
      }

      $placeholders = implode(",", array_fill(0, count($activityIds), "?"));
      $query = "SELECT activity_id, user_id FROM activity_participants WHERE activity_id IN (" . $placeholders . ") ORDER BY joined_at ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute($activityIds);

      $byActivity = [];
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $key = (int) $row["activity_id"];
         if (!isset($byActivity[$key])) {
            $byActivity[$key] = [];
         }
         $byActivity[$key][] = (int) $row["user_id"];
      }

      foreach ($activities as &$activity) {
         $id = (int) $activity["id"];
         $activity["participant_ids"] = $byActivity[$id] ?? [];
      }
      unset($activity);

      return $activities;
   }
}
