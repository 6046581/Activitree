<?php
require_once "autoload.php";

header("Content-Type: application/json");

function outputJson(int $statusCode, array $payload): void {
	http_response_code($statusCode);
	echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	exit;
}

function getApiRoute(): array {
	$resource = trim((string) ($_GET["resource"] ?? ""));
	$action = trim((string) ($_GET["action"] ?? ""));
	$parameter = filter_input(INPUT_GET, "parameter", FILTER_VALIDATE_INT);
	$parameter = $parameter !== false ? $parameter : null;

	$requestPath = parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH);
	$segments = array_values(array_filter(explode("/", trim((string) $requestPath, "/")), "strlen"));
	$apiIndex = array_search("api", $segments, true);

	if ($apiIndex !== false) {
		$routeOffset = $apiIndex + 1;

		if (($segments[$routeOffset] ?? "") === "index.php") {
			$routeOffset++;
		}

		if ($resource === "") {
			$resource = $segments[$routeOffset] ?? "";
		}

		if ($action === "") {
			$action = $segments[$routeOffset + 1] ?? "";
		}

		if ($parameter === null) {
			$parameterSegment = $segments[$routeOffset + 2] ?? null;

			if ($parameterSegment === null) {
				$parameterSegment = $segments[$routeOffset + 1] ?? null;
			}

			if ($parameterSegment !== null && ctype_digit($parameterSegment)) {
				$parameter = (int) $parameterSegment;
			}
		}
	}

	return [
		"resource" => $resource,
		"action" => $action,
		"parameter" => $parameter,
	];
}

function getRequestBody(): array {
	$raw = file_get_contents("php://input");

	if (!is_string($raw) || trim($raw) === "") {
		return $_POST ?: [];
	}

	$decoded = json_decode($raw, true);

	if (is_array($decoded)) {
		return $decoded;
	}

	return $_POST ?: [];
}

function getApiInfo(): array {
	return [
		"success" => true,
		"message" => "Party Planner API is running.",
		"availableEndpoints" => [
			[
				"method" => "GET",
				"path" => "/api/user/1",
				"query" => "/api/index.php?resource=user&parameter=1",
				"description" => "Fetch a user by id"
			],
			[
				"method" => "POST",
				"path" => "/api/auth/login",
				"query" => "/api/index.php?resource=auth&action=login",
				"description" => "Login with email and password"
			],
			[
				"method" => "GET",
				"path" => "/api/activities",
				"query" => "/api/index.php?resource=activities",
				"description" => "Fetch all activities"
			]
		]
	];
}

$route = getApiRoute();
$resource = $route["resource"];
$action = $route["action"];
$parameter = $route["parameter"];
$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

if ($resource === "") {
	if ($method !== "GET") {
		outputJson(405, [
			"success" => false,
			"error" => "Method not allowed",
			"allowedMethods" => ["GET"]
		]);
	}

	outputJson(200, getApiInfo());
}

try {
	$database = new Database();
	$conn = $database->getConnection();

	if ($resource === "user") {
		if ($method !== "GET") {
			outputJson(405, [
				"success" => false,
				"error" => "Method not allowed",
				"allowedMethods" => ["GET"]
			]);
		}

		if ($parameter === null || $parameter <= 0) {
			outputJson(400, [
				"success" => false,
				"error" => "Missing or invalid user id"
			]);
		}

		$user = new User($conn);
		$foundUser = $user->findPublicUserById($parameter);

		if ($foundUser === null) {
			outputJson(404, [
				"success" => false,
				"error" => "User not found"
			]);
		}

		outputJson(200, [
			"success" => true,
			"user" => $foundUser
		]);
	}

	if ($resource === "auth" && $action === "login") {
		if ($method !== "POST") {
			outputJson(405, [
				"success" => false,
				"error" => "Method not allowed",
				"allowedMethods" => ["POST"]
			]);
		}

		$body = getRequestBody();
		$email = trim((string) ($body["email"] ?? ""));
		$password = (string) ($body["password"] ?? "");

		if ($email === "" || $password === "") {
			outputJson(400, [
				"success" => false,
				"error" => "Email and password are required"
			]);
		}

		$user = new User($conn);
		$authenticatedUser = $user->authenticate($email, $password);

		if ($authenticatedUser === null) {
			outputJson(401, [
				"success" => false,
				"error" => "Invalid email or password"
			]);
		}

		$tokenPayload = $authenticatedUser["id"] . ":" . $authenticatedUser["email"] . ":" . time();
		$token = base64_encode($tokenPayload);

		outputJson(200, [
			"success" => true,
			"token" => $token,
			"user" => $authenticatedUser
		]);
	}

	if ($resource === "activities") {
		if ($method !== "GET") {
			outputJson(405, [
				"success" => false,
				"error" => "Method not allowed",
				"allowedMethods" => ["GET"]
			]);
		}

		$activities = new activity($conn);
		outputJson(200, [
			"success" => true,
			"activities" => $activities->getActivities()
		]);
	}

	outputJson(404, [
		"success" => false,
		"error" => "Resource not found",
		"requestedResource" => $resource,
		"availableEndpoints" => getApiInfo()["availableEndpoints"]
	]);
} catch (Throwable $exception) {
	outputJson(500, [
		"success" => false,
		"error" => "Internal server error",
		"details" => $exception->getMessage()
	]);
}