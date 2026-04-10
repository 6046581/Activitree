<?php
class ActivitiesController
{
   private $model;

   public function __construct()
   {
      $this->model = new Activities();
   }

   public function getAllActivities($params, $data)
   {
      // Get page parameters
      $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 100;
      $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

      // Return activities
      $rows = $this->model->getAllActivities($limit, $offset);
      foreach ($rows as &$row) {
         $row["photo_url"] = !empty($row["photo_path"]) ? buildPublicFileUrl($row["photo_path"]) : null;
      }
      unset($row);
      return ["code" => 200, "data" => ["data" => $rows]];
   }

   public function getActivityById($params, $data)
   {
      // Check if ID is provided and valid
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Return activity
      $row = $this->model->getActivityById($id);
      if (!$row) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      $row["photo_url"] = !empty($row["photo_path"]) ? buildPublicFileUrl($row["photo_path"]) : null;

      return ["code" => 200, "data" => ["data" => $row]];
   }

   public function createActivity($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      if (!is_array($data)) {
         return ["code" => 400, "data" => ["error" => "Invalid JSON body"]];
      }

      // Get input data
      $title = trim((string) ($data["title"] ?? ""));
      $description = isset($data["description"]) ? trim((string) $data["description"]) : null;
      $activity_type = strtolower(trim((string) ($data["activity_type"] ?? "indoor")));
      $status = strtolower(trim((string) ($data["status"] ?? "planned")));
      $activity_time = trim((string) ($data["activity_time"] ?? ""));
      $location_id = $data["location_id"] ?? null;

      if ($title === "" || mb_strlen($title) > 100) {
         return ["code" => 400, "data" => ["error" => "Title is required and must be 100 characters or fewer"]];
      }

      $allowedTypes = ["indoor", "outdoor"];
      if (!in_array($activity_type, $allowedTypes, true)) {
         return ["code" => 400, "data" => ["error" => "Invalid activity_type"]];
      }

      $allowedStatuses = ["planned", "cancelled", "completed"];
      if (!in_array($status, $allowedStatuses, true)) {
         return ["code" => 400, "data" => ["error" => "Invalid status"]];
      }

      $dt = DateTime::createFromFormat("Y-m-d H:i:s", $activity_time);
      if (!$dt || $dt->format("Y-m-d H:i:s") !== $activity_time) {
         return ["code" => 400, "data" => ["error" => "Invalid activity_time format, expected YYYY-MM-DD HH:MM:SS"]];
      }

      if ($location_id === "" || $location_id === null) {
         $location_id = null;
      } else {
         if (!is_numeric($location_id) || (int) $location_id <= 0) {
            return ["code" => 400, "data" => ["error" => "Invalid location_id"]];
         }

         $location_id = (int) $location_id;
         $locationModel = new Locations();
         if (!$locationModel->getLocationById($location_id)) {
            return ["code" => 400, "data" => ["error" => "Selected location does not exist"]];
         }
      }

      // Validate required fields
      if (!$title || !$activity_time) {
         return ["code" => 400, "data" => ["error" => "title and activity_time required"]];
      }

      // Create activity
      try {
         $created = $this->model->createActivity($title, $description, $activity_type, $status, $activity_time, $location_id, $auth["id"]);
      } catch (PDOException $e) {
         $sqlState = (string) $e->getCode();
         if ($sqlState === "22007") {
            return ["code" => 400, "data" => ["error" => "Invalid date/time value for activity_time"]];
         }

         if ($sqlState === "23000") {
            return ["code" => 400, "data" => ["error" => "Activity could not be created because of a data constraint", "message" => $e->getMessage()]];
         }

         return ["code" => 500, "data" => ["error" => "Database error while creating activity", "message" => $e->getMessage()]];
      }

      if ($created === false) {
         return ["code" => 500, "data" => ["error" => "Failed to create activity"]];
      }

      return ["code" => 201, "data" => ["id" => $created]];
   }

   public function getActivityParticipants($params, $data)
   {
      // Check if ID is provided and valid
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check if activity exists
      $existing = $this->model->getActivityById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Return participants
      $rows = $this->model->getActivityParticipants($id);
      foreach ($rows as &$row) {
         $row["avatar_url"] = !empty($row["avatar_path"]) ? buildPublicFileUrl($row["avatar_path"]) : null;
      }
      unset($row);

      return ["code" => 200, "data" => ["data" => $rows]];
   }

   public function joinActivity($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Check if ID is provided and valid
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check if activity exists
      $existing = $this->model->getActivityById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Avoid duplicate participation
      if ($this->model->isUserParticipating($id, $auth["id"])) {
         return ["code" => 409, "data" => ["error" => "Already joined"]];
      }

      // Join activity
      $ok = $this->model->joinActivity($id, $auth["id"], "participant");
      if (!$ok) {
         return ["code" => 500, "data" => ["error" => "Failed to join activity"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function leaveActivity($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Check if ID is provided and valid
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check if activity exists
      $existing = $this->model->getActivityById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Leave activity
      $left = $this->model->leaveActivity($id, $auth["id"]);
      if (!$left) {
         return ["code" => 404, "data" => ["error" => "You are not participating in this activity"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function updateActivity($params, $data)
   {
      // Get activity ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check auth
      $auth = denyUnauthorized();

      // Check if activity exists
      $existing = $this->model->getActivityById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Only allow update if user is admin or creator of the activity
      if (($auth["role"] ?? "") !== "admin" && (int) $existing["created_by"] !== (int) $auth["id"]) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      // Update fields that are provided in the request
      $title = $data["title"] ?? $existing["title"];
      $description = $data["description"] ?? $existing["description"];
      $activity_type = $data["activity_type"] ?? $existing["activity_type"];
      $status = $data["status"] ?? $existing["status"];
      $activity_time = $data["activity_time"] ?? $existing["activity_time"];
      $location_id = $data["location_id"] ?? $existing["location_id"];

      // Update activity
      $ok = $this->model->updateActivity($id, $title, $description, $activity_type, $status, $activity_time, $location_id);
      if (!$ok) {
         return ["code" => 500, "data" => ["error" => "Failed to update"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function deleteActivity($params, $data)
   {
      // Get activity ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check auth
      $auth = denyUnauthorized();

      // Delete activity if user is admin or creator of the activity
      $deleted = $this->model->deleteActivity($id, $auth["id"], $auth["role"] ?? "user");
      if ($deleted) {
         return ["code" => 200, "data" => ["ok" => true]];
      }

      return ["code" => 404, "data" => ["error" => "Not found or not allowed"]];
   }

   public function uploadActivityPhoto($params, $data)
   {
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      $auth = denyUnauthorized();

      $existing = $this->model->getActivityById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      if (($auth["role"] ?? "") !== "admin" && (int) $existing["created_by"] !== (int) $auth["id"]) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      $upload = $_FILES["file"] ?? ($_FILES["photo"] ?? null);
      if (!$upload) {
         return ["code" => 400, "data" => ["error" => "Missing upload file (use field 'file' or 'photo')"]];
      }

      $saved = storeUploadedImage($upload, "activity_photos", "activity_" . $id);
      if (!($saved["ok"] ?? false)) {
         return ["code" => 400, "data" => ["error" => $saved["error"] ?? "Upload failed"]];
      }

      $updated = $this->model->updateActivityPhotoPath($id, $saved["path"]);
      if (!$updated) {
         deleteUploadedFile($saved["path"]);
         return ["code" => 500, "data" => ["error" => "Failed to save activity photo path"]];
      }

      if (!empty($existing["photo_path"])) {
         deleteUploadedFile($existing["photo_path"]);
      }

      return [
         "code" => 200,
         "data" => [
            "ok" => true,
            "photo_path" => $saved["path"],
            "photo_url" => $saved["url"],
         ],
      ];
   }
}
