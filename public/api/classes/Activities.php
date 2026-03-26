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

    public function getActivityById($id, $limit, $offset)
    {
        $query = "SELECT id, title, description, activity_type, status, activity_date, activity_time, location_id, created_by FROM" . $this->table . "WHERE id = :id LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createActivity($title, $description, $activity_type, $status, $activity_date, $activity_time, $location_id, $created_by)
    {
        $query = "INSERT INTO activities (title, description, activity_type, status, activity_date, activity_time, location_id, created_by) VALUES (:title, :description, :activity_type, :status, :activity_date, :activity_time, :location_id, :created_by)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":activity_type", $activity_type);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":activity_date", $activity_date);
        $stmt->bindParam(":activity_time", $activity_time);
        $stmt->bindParam(":location_id", $location_id);
        $stmt->bindParam(":created_by", $created_by);
        return $stmt->execute();
    }

    public function updateActivities($id, $title, $description, $activity_type, $status, $activity_date, $activity_time, $location_id, $created_by)
    {
        $query = "UPDATE" . $this->table . "SET id = :id, title = :title, description = :description, activity_type = :activity_type, status = :status, activity_date = :activity_date, activity_time = :activity_time, location_id = :location_id, created_by = :created_by";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":activity_type", $activity_type);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":activity_date", $activity_date);
        $stmt->bindParam(":activity_time", $activity_time);
        $stmt->bindParam(":location_id", $location_id);
        $stmt->bindParam(":created_by", $created_by);
        return $stmt->execute();
    }

    public function deleteActivity($id, $requestUserId, $requestUserRole = 'user')
    {
        if ($requestUserRole === 'admin') {
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

    public function deleteActivitiesByUserId($user_id, $requestUserId, $requestUserRole = 'user')
    {
        if ($requestUserRole !== 'admin' && (int) $user_id !== (int) $requestUserId) {
            return false;
        }

        $query = "DELETE FROM " . $this->table . " WHERE created_by = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
