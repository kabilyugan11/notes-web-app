<?php
// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

// Basic information about the environment
$info = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Development Server',
    'api_path' => realpath(__DIR__ . '/api'),
    'workspace_api_path' => realpath(__DIR__ . '/../workspace/api'),
    'api_files' => []
];

// Check if API endpoint files exist
$endpoints = [
    'register' => '/api/endpoints/auth/register.php',
    'login' => '/api/endpoints/auth/login.php',
    'logout' => '/api/endpoints/auth/logout.php'
];

foreach ($endpoints as $name => $path) {
    $fullPath = __DIR__ . $path;
    $info['api_files'][$name] = [
        'path' => $path,
        'exists' => file_exists($fullPath),
        'readable' => is_readable($fullPath)
    ];
}

// Check MongoDB extension
$info['mongodb_extension'] = extension_loaded('mongodb');
$info['mongodb_class'] = class_exists('MongoDB\Client');

echo json_encode($info, JSON_PRETTY_PRINT);
?>
