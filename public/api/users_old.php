<?php
require_once 'db.php';
require_once 'autoload.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method)
{
    case 'GET':
        if (isset($_GET['id']))
        {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM users WHERE id=$id");
            $data = $result->fetch_assoc();

            http_response_code(200);
            echo json_encode($data);
        }
        else
        {
            $result = $conn->query("SELECT * FROM users");
            $users = [];
            while ($row = $result->fetch_assoc())
            {
                $users[] = $row;
            }

            http_response_code(200);
            echo json_encode($users);
        }
        break;

    case 'POST':
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];
        $conn->query("INSERT INTO users (name, email, age) VALUES ('$name', '$email', $age)");
        
        http_response_code(200);
        echo json_encode(["message" => "User added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $name = $input['name'];
        $email = $input['email'];
        $age = $input['age'];
        $conn->query("UPDATE users SET name='$name',
                     email='$email', age=$age WHERE id=$id");

        http_response_code(200);
        echo json_encode(["message" => "User updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM users WHERE id=$id");

        http_response_code(200);
        echo json_encode(["message" => "User deleted successfully"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();
