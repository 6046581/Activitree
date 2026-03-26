<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/classes/database.php';
require_once __DIR__ . '/classes/user.php';
require_once __DIR__ . '/classes/Activity.php';

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$apiPath = $uriPath;
$apiStart = strpos($uriPath, '/api/');
if ($apiStart !== false) {
    $apiPath = substr($uriPath, $apiStart);
}
$rawBody = file_get_contents('php://input');
$input = [];

if ($rawBody !== false && $rawBody !== '') {
    $input = json_decode($rawBody, true);
    if (!is_array($input)) {
        jsonResponse(['message' => 'Invalid JSON body'], 400);
    }
}

if ($method === 'GET' && ($apiPath === '/api' || $apiPath === '/api/')) {
    jsonResponse([
        'message' => 'Party Planner API',
        'routes' => [
            'GET /api/users/{id}',
            'POST /api/login',
            'POST /api/users',
            'PATCH /api/users/{id}',
            'DELETE /api/activities/{id}'
        ]
    ], 200);
}

try {
    $db = new Database();
    $users = new Users($db);
    $activities = new activities($db);
} catch (Throwable $e) {
    jsonResponse(['message' => 'Database connection failed'], 500);
}

$requestUserId = isset($_SERVER['HTTP_X_USER_ID']) ? (int) $_SERVER['HTTP_X_USER_ID'] : 0;
$requestUserRole = $_SERVER['HTTP_X_USER_ROLE'] ?? 'user';

if ($method === 'GET' && preg_match('#^/api/users/(\d+)$#', $apiPath, $match)) {
    $userId = (int) $match[1];
    $user = $users->getUserById($userId);

    if (!$user) {
        jsonResponse(['message' => 'User not found'], 404);
    }

    jsonResponse(['data' => $user], 200);
}

if ($method === 'POST' && $apiPath === '/api/login') {
    if (empty($input['email']) || empty($input['password'])) {
        jsonResponse(['message' => 'email and password are required'], 400);
    }

    $loggedInUser = $users->login((string) $input['email'], (string) $input['password']);
    if (!$loggedInUser) {
        jsonResponse(['message' => 'Invalid credentials'], 401);
    }

    jsonResponse([
        'message' => 'Login successful',
        'data' => $loggedInUser,
    ], 200);
}

if ($method === 'POST' && $apiPath === '/api/users') {
    if (empty($input['username']) || empty($input['email']) || empty($input['password'])) {
        jsonResponse(['message' => 'username, email, password are required'], 400);
    }

    $role = !empty($input['role']) ? (string) $input['role'] : 'user';
    $ok = $users->createUser(
        (string) $input['username'],
        (string) $input['email'],
        (string) $input['password'],
        $role
    );

    if (!$ok) {
        jsonResponse(['message' => 'User creation failed'], 500);
    }

    jsonResponse(['message' => 'User created'], 201);
}

if ($method === 'PATCH' && preg_match('#^/api/users/(\d+)$#', $apiPath, $match)) {
    $userId = (int) $match[1];

    if ($requestUserRole !== 'admin' && $requestUserId !== $userId) {
        jsonResponse(['message' => 'Forbidden'], 403);
    }

    if (empty($input['username']) || empty($input['email'])) {
        jsonResponse(['message' => 'username and email are required'], 400);
    }

    $role = $requestUserRole === 'admin' && !empty($input['role']) ? (string) $input['role'] : 'user';
    $ok = $users->updateUser($userId, (string) $input['username'], (string) $input['email'], $role);

    if (!$ok) {
        jsonResponse(['message' => 'User update failed'], 500);
    }

    jsonResponse(['message' => 'User updated'], 200);
}

if ($method === 'DELETE' && preg_match('#^/api/activities/(\d+)$#', $apiPath, $match)) {
    $activityId = (int) $match[1];
    $ok = $activities->deleteActivity($activityId, $requestUserId, $requestUserRole);

    if (!$ok) {
        jsonResponse(['message' => 'Not allowed or activity not found'], 403);
    }

    jsonResponse(['message' => 'Activity deleted'], 200);
}

jsonResponse([
    'message' => 'Route not found',
    'method' => $method,
    'path' => $apiPath,
], 404);