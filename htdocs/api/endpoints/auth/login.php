<?php
session_start();
require_once '../classes/Database.php';
require_once '../classes/User.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!$data) {
        // Try to get form data if JSON failed
        $data = $_POST;
    }

    if (isset($data['email']) && isset($data['password'])) {
        try {
            $usernameOrEmail = htmlspecialchars($data['email']);
            $password = $data['password']; // Don't escape password before verification

            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            
            $loginResult = $user->login($usernameOrEmail, $password);

            if ($loginResult) {
                $_SESSION['user_id'] = $loginResult['id'];
                $_SESSION['username'] = $loginResult['username'];
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $loginResult['id'],
                        'username' => $loginResult['username']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>