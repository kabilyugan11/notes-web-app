<?php
// This script directly includes the register.php file from the workspace
// to bypass any routing or .htaccess issues

// Define the path to the register.php file
$registerPath = __DIR__ . '/../workspace/api/endpoints/auth/register.php';

// Check if the file exists
if (!file_exists($registerPath)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Register API file not found at: ' . $registerPath,
        'current_dir' => __DIR__,
        'parent_dir' => dirname(__DIR__),
        'grandparent_dir' => dirname(dirname(__DIR__))
    ]);
    exit;
}

// If this is a POST request, forward it to the register.php file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Make sure the POST data is available to the included file
    $_POST = array_merge($_POST, []);
    
    // For debugging purposes
    error_log("Direct register received POST data: " . print_r($_POST, true));
    
    // Check if we're receiving form data
    if (!empty($_POST)) {
        error_log("Using POST data");
    } 
    // Or raw JSON
    else {
        $rawData = file_get_contents('php://input');
        if (!empty($rawData)) {
            error_log("Using raw input: " . $rawData);
            $_POST = json_decode($rawData, true) ?: [];
        }
    }
    
    // Include the register.php file
    include($registerPath);
    exit;
}

// Otherwise, show a simple form for testing
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Registration Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Direct Registration Test</h1>
        <p>This page directly includes the register.php file from the workspace to bypass any routing issues.</p>
        
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

        <h2>API Information</h2>
        <ul>
            <li>Register API Path: <?php echo $registerPath; ?></li>
            <li>File exists: <?php echo file_exists($registerPath) ? 'Yes' : 'No'; ?></li>
            <li>File readable: <?php echo is_readable($registerPath) ? 'Yes' : 'No'; ?></li>
            <li>File size: <?php echo file_exists($registerPath) ? filesize($registerPath) . ' bytes' : 'N/A'; ?></li>
        </ul>

        <div class="mt-3">
            <a href="setup_api.php" class="btn btn-primary">Setup API Files</a>
            <a href="verify_mongodb.php" class="btn btn-info">Verify MongoDB</a>
            <a href="api_status.php" class="btn btn-secondary">Check API Status</a>
            <a href="register.html" class="btn btn-warning">Back to Register</a>
        </div>
    </div>
</body>
</html>
