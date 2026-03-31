<?php
class Router
{
   /**
    * Dispatch the incoming HTTP request to the right function based on the defined routes.
    * @param array $routes
    * @return mixed
    * @throws RuntimeException if no matching route or function is found
    */
   public static function executeRoute(array $routes)
   {
      // Get HTTP method and request URI path
      $method = $_SERVER["REQUEST_METHOD"] ?? "GET";
      $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) ?: "/";

      // Remove script directory prefix (so router works mounted under a subpath)
      $scriptDir = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));
      if ($scriptDir !== "/" && $scriptDir !== "\\" && $scriptDir !== ".") {
         if (strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
         }
      }

      // Normalize path and construct route key
      $path = "/" . trim($uri, "/");
      $routeKey = strtoupper($method) . " " . rtrim($path, "/");

      // Try get exact matching paths first
      if (isset($routes[$routeKey])) {
         return self::invokeFunction($routes[$routeKey], []);
      }

      // Try matching routes with parameters (eg: /users/{id})
      foreach ($routes as $route => $function) {
         $parts = explode(" ", $route, 2);
         if (count($parts) !== 2) {
            continue;
         }
         [$rmethod, $rpath] = $parts;
         if (strtoupper($rmethod) !== strtoupper($method)) {
            continue;
         }

         // Convert route path with {param} to regex pattern and match against request path
         $pattern = "#^" . preg_replace("#\{[^/]+\}#", "([^/]+)", $rpath) . '$#';
         if (preg_match($pattern, $path, $matches)) {
            array_shift($matches); // drop full match
            return self::invokeFunction($function, $matches);
         }
      }

      // No matching route found
      http_response_code(404);
      return ["code" => 404, "data" => ["error" => "Not found"]];
   }

   /**
    * Run the route function, which can be a callable function or a controller method string like "UsersController@method".
    * Parses JSON request body and passes it as $data to the function.
    * @param mixed $function
    * @param array $params
    * @return mixed
    * @throws RuntimeException if function is not callable or controller/method not found
    */
   private static function invokeFunction($function, array $params)
   {
      // Read JSON request body once and pass parsed data to handlers
      $body = file_get_contents("php://input");
      $data = json_decode($body, true);

      // If function is a callable, call it
      if (is_callable($function)) {
         return call_user_func($function, $params, $data);
      }

      // If function is a controller string like "UsersController@method" or "UsersController::method"
      if (is_string($function)) {
         // Support both Class@method and Class::method syntax
         if (strpos($function, "@") !== false || strpos($function, "::") !== false) {
            $parts = strpos($function, "@") !== false ? explode("@", $function, 2) : explode("::", $function, 2);

            if (count($parts) !== 2) {
               throw new RuntimeException("Invalid controller handler format");
            }

            [$className, $method] = $parts;

            if (!class_exists($className)) {
               throw new RuntimeException("Controller class $className not found");
            }

            $controller = new $className();

            if (!method_exists($controller, $method)) {
               throw new RuntimeException("Method $method not found on controller $className");
            }

            return $controller->$method($params, $data);
         }

         // If function is a bare function name, call it
         if (function_exists($function)) {
            return call_user_func($function, $params, $data);
         }
      }

      throw new RuntimeException("Route function is not callable");
   }
}
