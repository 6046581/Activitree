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
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$id = $id !== false ? $id : null;

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

		if ($id === null) {
			$idSegment = $segments[$routeOffset + 1] ?? null;

			if ($idSegment !== null && ctype_digit($idSegment)) {
				$id = (int) $idSegment;
			}
		}
	}

	return [
		"resource" => $resource,
		"id" => $id,
	];
}

function getApiInfo(): array {
	return [
		"success" => true,
		"message" => "Party Planner API is running.",
		"availableEndpoints" => [
			[
				"method" => "GET",
				"path" => "/api/user/1",
				"query" => "/api/index.php?resource=user&id=1",
				"description" => "Fetch a user by id"
			]
		]
	];
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
	outputJson(405, [
		"success" => false,
		"error" => "Method not allowed",
		"allowedMethods" => ["GET"]
	]);
}

$route = getApiRoute();
$resource = $route["resource"];
$id = $route["id"];

if ($resource === "") {
	outputJson(200, getApiInfo());
}

if ($resource !== "user") {
	outputJson(404, [
		"success" => false,
		"error" => "Resource not found",
		"requestedResource" => $resource,
		"availableEndpoints" => getApiInfo()["availableEndpoints"]
	]);
}

try {
	$database = new Database();
	$user = new User($database->getConnection());
	$user->outputUserByIdJson($id);
} catch (Throwable $exception) {
	outputJson(500, [
		"success" => false,
		"error" => "Internal server error"
	]);
}