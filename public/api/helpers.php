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
