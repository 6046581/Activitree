<?php
require_once "autoload.php";
require_once "router.php";
require_once "helpers.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Route map: maps HTTP method + path to controller functions. The router will parse the request and call the function.
// The function can be a callable or a string in the format "Controller@method" or "Controller::method".
// The router will instantiate the controller and call the method, passing URL parameters and parsed JSON body as arguments.
$routes = [
   // Users
   "POST /users/login" => "UsersController@login",
   "POST /users/logout" => "UsersController@logout",
   "GET /users" => "UsersController@getAllUsers",
   "GET /users/{id}" => "UsersController@getUserById",
   "POST /users" => "UsersController@createUser",
   "PUT /users/{id}" => "UsersController@updateUser",
   "DELETE /users/{id}" => "UsersController@deleteUser",

   // Activities
   "GET /activities" => "ActivitiesController@getAllActivities",
   "GET /activities/{id}" => "ActivitiesController@getActivityById",
   "GET /activities/{id}/participants" => "ActivitiesController@getActivityParticipants",
   "POST /activities/{id}/join" => "ActivitiesController@joinActivity",
   "DELETE /activities/{id}/leave" => "ActivitiesController@leaveActivity",
   "POST /activities" => "ActivitiesController@createActivity",
   "PUT /activities/{id}" => "ActivitiesController@updateActivity",
   "DELETE /activities/{id}" => "ActivitiesController@deleteActivity",

   // Locations
   "GET /locations" => "LocationsController@getAllLocations",
   "GET /locations/{id}" => "LocationsController@getLocationById",
];

// Try to run the function mapped to the requested route
try {
   $result = Router::executeRoute($routes);
   if (is_array($result) && isset($result["code"]) && array_key_exists("data", $result)) {
      sendJson($result["data"], $result["code"]);
   }

   // Fallback: return whatever handler returned
   sendJson($result, 200);
} catch (Throwable $e) {
   sendJson(["error" => "Server error", "message" => $e->getMessage()], 500);
}
