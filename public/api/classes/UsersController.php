<?php
class UsersController
{
   private $model;

   public function __construct()
   {
      $this->model = new Users();
   }

   public function loginUser($params, $data)
   {
      // Get input data
      $email = $data["email"] ?? null;
      $password = $data["password"] ?? null;
      if (!$email || !$password) {
         return ["code" => 400, "data" => ["error" => "email and password required"]];
      }

      // Attempt login
      $user = $this->model->loginUser($email, $password);
      if (!$user) {
         return ["code" => 401, "data" => ["error" => "Invalid credentials"]];
      }

      // Generate token and save it
      $token = bin2hex(random_bytes(16));
      $saved = $this->model->setToken($user["id"], $token);
      if (!$saved) {
         return ["code" => 500, "data" => ["error" => "Failed to persist login token"]];
      }

      // Return user data and token (excluding password)
      unset($user["password"]);
      return ["code" => 200, "data" => ["user" => $user, "token" => $token]];
   }

   public function logoutUser($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Logout by clearing the token
      $this->model->setToken($auth["id"], null);

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function signupUser($params, $data)
   {
      // Get input data
      $username = $data["username"] ?? null;
      $email = $data["email"] ?? null;
      $password = $data["password"] ?? null;

      // Validate required fields
      if (!$username || !$email || !$password) {
         return ["code" => 400, "data" => ["error" => "username,email,password required"]];
      }

      if ($this->model->usernameExists($username)) {
         return ["code" => 409, "data" => ["error" => "Username already in use"]];
      }

      if ($this->model->emailExists($email)) {
         return ["code" => 409, "data" => ["error" => "Email already in use"]];
      }

      // Create user
      $created = $this->model->signupUser($username, $email, $password);
      if (!$created) {
         return ["code" => 500, "data" => ["error" => "Failed to create user"]];
      }

      return ["code" => 201, "data" => ["id" => $created]];
   }

   public function getAllUsers($params, $data)
   {
      // Check auth and require admin role
      $auth = denyUnauthorized("admin");

      // Get page parameters
      $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 100;
      $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

      // Return users
      $rows = $this->model->getAllUsers($limit, $offset);
      foreach ($rows as &$row) {
         $row["avatar_url"] = !empty($row["avatar_path"]) ? buildPublicFileUrl($row["avatar_path"]) : null;
      }
      unset($row);

      return ["code" => 200, "data" => ["data" => $rows]];
   }

   public function getUserById($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Get user ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Only allow access if user is admin or requesting their own data
      if (($auth["role"] ?? "") !== "admin" && (int) $auth["id"] !== $id) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      // Return user
      $row = $this->model->getUserById($id);
      if (!$row) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      $row["avatar_url"] = !empty($row["avatar_path"]) ? buildPublicFileUrl($row["avatar_path"]) : null;

      return ["code" => 200, "data" => ["data" => $row]];
   }

   public function updateUser($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Get user ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check if user exists
      $existing = $this->model->getUserById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Only allow update if user is admin or updating their own data
      if (($auth["role"] ?? "") !== "admin" && (int) $auth["id"] !== $id) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      // Update fields that are provided in the request
      $username = $data["username"] ?? $existing["username"];
      $email = $data["email"] ?? $existing["email"];

      // Update user
      $ok = $this->model->updateUser($id, $username, $email);
      if (!$ok) {
         return ["code" => 500, "data" => ["error" => "Failed to update"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function updateUserPassword($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Get user ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Check if user exists
      $existing = $this->model->getUserById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      // Only allow update if user is admin or updating their own password
      if (($auth["role"] ?? "") !== "admin" && (int) $auth["id"] !== $id) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      // Get old and new password from request
      $oldPassword = $data["old_password"] ?? null;
      $newPassword = $data["new_password"] ?? null;
      if (!$newPassword) {
         return ["code" => 400, "data" => ["error" => "New password required"]];
      }

      // Verify old password
      if (!password_verify($oldPassword, $existing["password"])) {
         return ["code" => 400, "data" => ["error" => "Invalid old password"]];
      }

      // Update password
      $ok = $this->model->updateUserPassword($id, $newPassword);
      if (!$ok) {
         return ["code" => 500, "data" => ["error" => "Failed to update password"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function deleteUser($params, $data)
   {
      // Check auth
      $auth = denyUnauthorized();

      // Get user ID from URL and validate
      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      // Only allow delete if user is admin or deleting their own account
      if (($auth["role"] ?? "") !== "admin" && (int) $auth["id"] !== $id) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      // Delete user
      $ok = $this->model->deleteUser($id);
      if (!$ok) {
         return ["code" => 500, "data" => ["error" => "Failed to delete"]];
      }

      return ["code" => 200, "data" => ["ok" => true]];
   }

   public function uploadAvatar($params, $data)
   {
      $auth = denyUnauthorized();

      $id = isset($params[0]) ? (int) $params[0] : null;
      if (!$id) {
         return ["code" => 400, "data" => ["error" => "Invalid id"]];
      }

      if (($auth["role"] ?? "") !== "admin" && (int) $auth["id"] !== $id) {
         return ["code" => 403, "data" => ["error" => "Forbidden"]];
      }

      $existing = $this->model->getUserById($id);
      if (!$existing) {
         return ["code" => 404, "data" => ["error" => "Not found"]];
      }

      $upload = $_FILES["file"] ?? ($_FILES["avatar"] ?? ($_FILES["avatar"] ?? null));
      if (!$upload) {
         return ["code" => 400, "data" => ["error" => "Missing upload file (use field 'file', 'avatar', or 'avatar')"]];
      }

      $saved = storeUploadedImage($upload, "avatars", "user_" . $id);
      if (!($saved["ok"] ?? false)) {
         return ["code" => 400, "data" => ["error" => $saved["error"] ?? "Upload failed"]];
      }

      $updated = $this->model->updateAvatarPath($id, $saved["path"]);
      if (!$updated) {
         deleteUploadedFile($saved["path"]);
         return ["code" => 500, "data" => ["error" => "Failed to save avatar path"]];
      }

      if (!empty($existing["avatar_path"])) {
         deleteUploadedFile($existing["avatar_path"]);
      }

      return [
         "code" => 200,
         "data" => [
            "ok" => true,
            "avatar_path" => $saved["path"],
            "avatar_url" => $saved["url"],
         ],
      ];
   }
}
