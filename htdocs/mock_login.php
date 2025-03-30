<?php
// This script directly processes login without MongoDB dependency
// by using a file-based mock database implementation

// Include our mock database implementation
require_once __DIR__ . '/mock/Database.php';

// Create a log file for debugging
$logFile = __DIR__ . '/mock_login_log.txt';
file_put_contents($logFile, "Login attempt: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Function to output JSON responses
function outputJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// If this is a POST request, process the login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the content type
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    file_put_contents($logFile, "Content-Type: $contentType\n", FILE_APPEND);
    
    // Get the form data
    $data = [];
    
    // Handle form data
    if (strpos($contentType, 'application/x-www-form-urlencoded') !== false || 
        strpos($contentType, 'multipart/form-data') !== false) {
        $data = $_POST;
        file_put_contents($logFile, "Processing form data\n", FILE_APPEND);
    } 
    // Handle JSON data
    else if (strpos($contentType, 'application/json') !== false) {
        $rawData = file_get_contents('php://input');
        file_put_contents($logFile, "Raw JSON input: $rawData\n", FILE_APPEND);
        $data = json_decode($rawData, true) ?: [];
    }
    // Try both as fallback
    else {
        $data = $_POST;
        if (empty($data)) {
            $rawData = file_get_contents('php://input');
            file_put_contents($logFile, "Raw input: $rawData\n", FILE_APPEND);
            $data = json_decode($rawData, true) ?: [];
        }
    }
    
    file_put_contents($logFile, "Processed data: " . print_r($data, true) . "\n", FILE_APPEND);
    
    // Validate the data
    if ((isset($data['username']) || isset($data['email'])) && isset($data['password'])) {
        try {
            // Create database connection
            $database = new Database();
            $db = $database->getConnection();
            
            // Get database handle
            $dbHandle = $db->selectDatabase('notes_app');
            $usersCollection = $dbHandle->users;
            
            // Check if user exists by username or email
            $username = isset($data['username']) ? $data['username'] : '';
            $password = $data['password'];
            
            // Search conditions
            $searchConditions = [];
            // If username looks like an email, search for both username and email
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $searchConditions = [
                    '$or' => [
                        ['username' => $username],
                        ['email' => $username]
                    ]
                ];
                file_put_contents($logFile, "Searching by email: $username\n", FILE_APPEND);
            } else {
                $searchConditions = ['username' => $username];
                file_put_contents($logFile, "Searching by username: $username\n", FILE_APPEND);
            }
            
            // Find the user
            $user = $usersCollection->findOne($searchConditions);
            
            if (!$user) {
                file_put_contents($logFile, "User not found: $username\n", FILE_APPEND);
                outputJSON([
                    "success" => false,
                    "message" => "Login failed: Invalid username or password."
                ]);
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                file_put_contents($logFile, "Password verification failed for user: $username\n", FILE_APPEND);
                outputJSON([
                    "success" => false,
                    "message" => "Login failed: Invalid username or password."
                ]);
            }
            
            // Login successful
            file_put_contents($logFile, "Login successful for user: $username\n", FILE_APPEND);
            
            // Remove password from user data before sending to client
            unset($user['password']);
            
            // Return success with user data
            outputJSON([
                "success" => true,
                "message" => "Login successful!",
                "user" => $user
            ]);
            
        } catch (Exception $e) {
            file_put_contents($logFile, "Error: " . $e->getMessage() . "\n", FILE_APPEND);
            outputJSON([
                "success" => false,
                "message" => "Server error: " . $e->getMessage()
            ]);
        }
    } else {
        $missing = [];
        if (!isset($data['username']) && !isset($data['email'])) $missing[] = 'username/email';
        if (!isset($data['password'])) $missing[] = 'password';
        
        file_put_contents($logFile, "Missing fields: " . implode(', ', $missing) . "\n", FILE_APPEND);
        
        outputJSON([
            "success" => false,
            "message" => "Invalid input. Missing fields: " . implode(', ', $missing)
        ]);
    }
    exit;
}

// Otherwise, show a simple form for testing
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mock Login Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Mock Login Test</h1>
        <p>This page implements a file-based mock login to bypass MongoDB dependency issues.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This implementation authenticates against file-stored user data rather than a real MongoDB database.
            It's only for testing and development purposes.
        </div>
        
        <h2>Login Form</h2>
        <form id="testForm" method="post" class="mb-4">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <div class="mt-3">
            <a href="login.html" class="btn btn-warning">Back to Login</a>
        </div>
    </div>
</body>
</html> 