<?php
// This script directly processes registration without MongoDB dependency
// by using a file-based mock database implementation

// Define the path to the workspace root
$workspaceRoot = realpath(__DIR__ . '/../workspace');

// Include our mock database implementation
require_once __DIR__ . '/mock/Database.php';

// Create a log file for debugging
$logFile = __DIR__ . '/mock_register_log.txt';
file_put_contents($logFile, "Registration attempt: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Function to output JSON responses
function outputJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// If this is a POST request, process the registration
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
    if (isset($data['username']) && isset($data['password']) && isset($data['email'])) {
        try {
            file_put_contents($logFile, "Validation passed, processing registration\n", FILE_APPEND);
            
            // Create database connection
            $database = new Database();
            $db = $database->getConnection();
            
            // Get database handle
            $dbHandle = $db->selectDatabase('notes_app');
            $usersCollection = $dbHandle->users;
            
            // Check if user already exists
            $existingUser = $usersCollection->findOne([
                '$or' => [
                    ['username' => $data['username']],
                    ['email' => $data['email']]
                ]
            ]);
            
            if ($existingUser) {
                file_put_contents($logFile, "User already exists: " . $data['username'] . " or " . $data['email'] . "\n", FILE_APPEND);
                outputJSON([
                    "success" => false, 
                    "message" => "User registration failed. Username or email already exists."
                ]);
            }
            
            // Create the new user
            $username = htmlspecialchars($data['username']);
            $email = htmlspecialchars($data['email']);
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            
            file_put_contents($logFile, "Creating user: $username, $email\n", FILE_APPEND);
            
            // Insert the user
            $result = $usersCollection->insertOne([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'createdAt' => new MongoDB\BSON\UTCDateTime(),
                'updatedAt' => new MongoDB\BSON\UTCDateTime()
            ]);
            
            file_put_contents($logFile, "User created successfully\n", FILE_APPEND);
            
            // Return success
            outputJSON([
                "success" => true,
                "message" => "User registered successfully."
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
        if (!isset($data['username'])) $missing[] = 'username';
        if (!isset($data['email'])) $missing[] = 'email';
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
    <title>Mock Registration Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Mock Registration Test</h1>
        <p>This page implements a file-based mock database to bypass MongoDB dependency issues.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This implementation stores data in files rather than a real MongoDB database.
            It's only for testing and development purposes.
        </div>
        
        <h2>Registration Form</h2>
        <form id="testForm" method="post" class="mb-4">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="mt-3">
            <a href="register.html" class="btn btn-warning">Back to Register</a>
        </div>
    </div>
</body>
</html>
