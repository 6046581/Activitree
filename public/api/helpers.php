<?php

/**
 * Sends a JSON response with the specified data and HTTP status code.
 *
 * @param array $data The data to include in the JSON response.
 * @param int $code The HTTP status code.
 */
function sendJson($data, $code = 200)
{
   http_response_code($code);
   echo json_encode($data);
   exit();
}

/**
 * Gets the user authentication token from the Authorization header.
 *
 * @return string|null The authentication token or null if not found.
 */
function getRequestToken()
{
   $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? ($_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? null);

   if (!$authHeader) {
      $authHeader = getenv("HTTP_AUTHORIZATION") ?: null;
   }

   if (!$authHeader && function_exists("apache_request_headers")) {
      $hdrs = apache_request_headers();

      foreach ($hdrs as $name => $value) {
         if (strtolower($name) === "authorization" && !empty($value)) {
            $authHeader = $value;
            break;
         }
      }
   }

   if (!$authHeader) {
      return null;
   }
   if (stripos($authHeader, "Bearer ") === 0) {
      return trim(substr($authHeader, 7));
   }

   return trim($authHeader);
}

/**
 * Denies access if the user is not authorized or does not have the required role.
 *
 * @param string|null $requiredRole Optional required role for access.
 * @return array The user data if authorized.
 */
function denyUnauthorized($requiredRole = null)
{
   $token = getRequestToken();
   if (!$token) {
      sendJson(["error" => "Unauthorized"], 401);
      exit();
   }

   $users = new Users();
   $user = $users->getUserByToken($token);
   if (!$user) {
      sendJson(["error" => "Unauthorized"], 401);
      exit();
   }

   if ($requiredRole && ($user["role"] ?? "") !== $requiredRole) {
      sendJson(["error" => "Forbidden"], 403);
      exit();
   }

   return $user;
}

/**
 * Build a public URL for a relative file path under the public folder.
 *
 * @param string $relativePath
 * @return string
 */
function buildPublicFileUrl($relativePath)
{
   $normalized = ltrim(str_replace("\\", "/", (string) $relativePath), "/");
   if ($normalized === "") {
      return "/";
   }

   $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "/"));
   $publicBase = preg_replace("#/api/?$#", "", $scriptDir);
   $publicBase = rtrim((string) $publicBase, "/");

   if ($publicBase === "" || $publicBase === ".") {
      return "/" . $normalized;
   }

   return $publicBase . "/" . $normalized;
}

/**
 * Delete a previously stored upload under public/uploads.
 *
 * @param string|null $relativePath
 * @return void
 */
function deleteUploadedFile($relativePath)
{
   if (!$relativePath) {
      return;
   }

   $normalized = ltrim(str_replace("\\", "/", (string) $relativePath), "/");
   if ($normalized === "" || strpos($normalized, "uploads/") !== 0 || strpos($normalized, "..") !== false) {
      return;
   }

   $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $normalized);
   if (is_file($fullPath)) {
      @unlink($fullPath);
   }
}

/**
 * Persist an uploaded image file in public/uploads/{subFolder}.
 *
 * @param array $file The $_FILES entry.
 * @param string $subFolder Folder under uploads (e.g. "profile_pictures").
 * @param string $prefix File name prefix.
 * @return array
 */
function storeUploadedImage(array $file, $subFolder, $prefix = "image")
{
   if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
      return ["ok" => false, "error" => "No file uploaded or upload failed"];
   }

   $tmpPath = $file["tmp_name"] ?? "";
   if ($tmpPath === "" || !is_uploaded_file($tmpPath)) {
      return ["ok" => false, "error" => "Invalid upload"];
   }

   $maxBytes = 8 * 1024 * 1024;
   $size = (int) ($file["size"] ?? 0);
   if ($size <= 0 || $size > $maxBytes) {
      return ["ok" => false, "error" => "File must be between 1 byte and 8MB"];
   }

   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $mime = $finfo ? finfo_file($finfo, $tmpPath) : null;
   if ($finfo) {
      finfo_close($finfo);
   }

   $allowed = [
      "image/jpeg" => "jpg",
      "image/png" => "png",
      "image/webp" => "webp",
      "image/gif" => "gif",
   ];
   if (!$mime || !isset($allowed[$mime])) {
      return ["ok" => false, "error" => "Only JPG, PNG, WEBP, and GIF images are allowed"];
   }

   $safeFolder = trim(str_replace("\\", "/", (string) $subFolder), "/");
   if ($safeFolder === "" || strpos($safeFolder, "..") !== false) {
      return ["ok" => false, "error" => "Invalid target folder"];
   }

   $targetRelativeDir = "uploads/" . $safeFolder;
   $targetAbsoluteDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $targetRelativeDir);
   if (!is_dir($targetAbsoluteDir) && !mkdir($targetAbsoluteDir, 0755, true) && !is_dir($targetAbsoluteDir)) {
      return ["ok" => false, "error" => "Failed to create upload directory"];
   }

   $filename = sprintf("%s_%s.%s", $prefix, bin2hex(random_bytes(12)), $allowed[$mime]);
   $relativePath = $targetRelativeDir . "/" . $filename;
   $absolutePath = $targetAbsoluteDir . DIRECTORY_SEPARATOR . $filename;

   if (!move_uploaded_file($tmpPath, $absolutePath)) {
      return ["ok" => false, "error" => "Failed to save uploaded file"];
   }

   return [
      "ok" => true,
      "path" => $relativePath,
      "url" => buildPublicFileUrl($relativePath),
   ];
}
