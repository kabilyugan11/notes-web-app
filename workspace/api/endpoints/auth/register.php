<?php
// Ensure proper JSON output even when errors occur
function outputJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Set up error handler to catch all PHP errors and return as JSON
function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    $logFile = __DIR__ . '/register_log.txt';
    file_put_contents($logFile, "PHP Error: [$errno] $errstr in $errfile on line $errline\n", FILE_APPEND);
    outputJSON([
        "success" => false,
        "message" => "Server error: $errstr",
        "file" => basename($errfile),
        "line" => $errline
    ]);
    return true;
}
set_error_handler("jsonErrorHandler");

// Register exception handler
set_exception_handler(function($e) {
    $logFile = __DIR__ . '/register_log.txt';
    file_put_contents($logFile, "Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n", FILE_APPEND);
    outputJSON([
        "success" => false,
        "message" => "Server exception: " . $e->getMessage(),
        "file" => basename($e->getFile()),
        "line" => $e->getLine()
    ]);
});

// Always set content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Define base path to the workspace directory 
$workspaceRoot = realpath(__DIR__ . '/../../../');

// Log directory path for debugging
$logFile = __DIR__ . '/register_log.txt';
file_put_contents($logFile, "Registration attempt: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents($logFile, "Workspace root: " . $workspaceRoot . "\n", FILE_APPEND);
file_put_contents($logFile, "PHP version: " . phpversion() . "\n", FILE_APPEND);
file_put_contents($logFile, "Loaded extensions: " . implode(', ', get_loaded_extensions()) . "\n", FILE_APPEND);

// Check for MongoDB extension
if (!extension_loaded('mongodb')) {
    file_put_contents($logFile, "ERROR: MongoDB extension is not loaded\n", FILE_APPEND);
    outputJSON(["success" => false, "message" => "Server configuration error: MongoDB extension not loaded"]);
}

// Include required files with absolute paths
try {
    require_once $workspaceRoot . '/api/classes/Database.php';
    require_once $workspaceRoot . '/api/classes/User.php';
} catch (Exception $e) {
    file_put_contents($logFile, "ERROR: Failed to include required files: " . $e->getMessage() . "\n", FILE_APPEND);
    outputJSON(["success" => false, "message" => "Server configuration error: " . $e->getMessage()]);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable displaying errors directly

// Get POST data 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Try to get form data first
    $data = $_POST;
    file_put_contents($logFile, "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
    
    // If empty, try JSON
    if (empty($data)) {
        $json = file_get_contents("php://input");
        file_put_contents($logFile, "Raw request data: " . $json . "\n", FILE_APPEND);
        $data = json_decode($json, true);
    }
    
    // Double-check the data we're working with
    file_put_contents($logFile, "Final data for processing: " . print_r($data, true) . "\n", FILE_APPEND);
    
    if (isset($data['username']) && isset($data['password']) && isset($data['email'])) {
        try {
            file_put_contents($logFile, "Creating database connection\n", FILE_APPEND);
            $database = new Database();
            $db = $database->getConnection();
            
            file_put_contents($logFile, "Database connection established\n", FILE_APPEND);
            
            $user = new User($db);
            
            $username = htmlspecialchars($data['username']);
            $email = htmlspecialchars($data['email']);
            // Hash the password for security
            $password = password_hash($data['password'], PASSWORD_BCRYPT);

            file_put_contents($logFile, "Attempting to register user: $username, $email\n", FILE_APPEND);
            
            // Register the user
            $result = $user->register($username, $email, $password);
            
            file_put_contents($logFile, "Register result: " . ($result ? "Success" : "Failed") . "\n", FILE_APPEND);
            
            if ($result) {
                outputJSON(["success" => true, "message" => "User registered successfully."]);
            } else {
                outputJSON(["success" => false, "message" => "User registration failed. Username or email may already exist."]);
            }
        } catch (Exception $e) {
            file_put_contents($logFile, "Registration error: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($logFile, "Error trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
            outputJSON(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    } else {
        $missing = [];
        if (!isset($data['username'])) $missing[] = 'username';
        if (!isset($data['email'])) $missing[] = 'email';
        if (!isset($data['password'])) $missing[] = 'password';
        
        $errorMsg = "Invalid input. Missing fields: " . implode(', ', $missing);
        file_put_contents($logFile, $errorMsg . "\n", FILE_APPEND);
        outputJSON(["success" => false, "message" => $errorMsg]);
    }
} else {
    outputJSON(["success" => false, "message" => "Invalid request method. Only POST is allowed."]);
}
?>