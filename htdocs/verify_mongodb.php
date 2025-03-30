<?php
// Script to verify MongoDB connection and configuration

// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

echo "<h1>MongoDB Verification</h1>";

// Check if MongoDB extension is installed
if (!extension_loaded('mongodb')) {
    echo "<p style='color: red;'>ERROR: MongoDB PHP extension is not installed!</p>";
    echo "<p>Please install the MongoDB PHP extension using:</p>";
    echo "<pre>sudo pecl install mongodb</pre>";
    echo "<p>Then add <code>extension=mongodb.so</code> to your php.ini file.</p>";
    exit;
} else {
    echo "<p style='color: green;'>MongoDB PHP extension is installed.</p>";
}

// Verify MongoDB client class exists
if (!class_exists('MongoDB\Client')) {
    echo "<p style='color: red;'>ERROR: MongoDB\Client class not found!</p>";
    echo "<p>Please install the MongoDB PHP library using Composer:</p>";
    echo "<pre>composer require mongodb/mongodb</pre>";
    exit;
} else {
    echo "<p style='color: green;'>MongoDB\Client class is available.</p>";
}

// Try to connect to MongoDB
try {
    // Include the Database class
    require_once __DIR__ . '/api/classes/Database.php';
    
    echo "<p>Creating Database connection...</p>";
    $database = new Database();
    $client = $database->getConnection();
    
    echo "<p style='color: green;'>Successfully connected to MongoDB!</p>";
    
    // Try to list all databases
    echo "<h2>Available Databases:</h2>";
    echo "<ul>";
    $databaseList = $client->listDatabases();
    foreach ($databaseList as $db) {
        echo "<li>" . $db->getName() . "</li>";
    }
    echo "</ul>";
    
    // Try to verify the 'notes_app' database and collections
    $db = $client->selectDatabase('notes_app');
    
    echo "<h2>Collections in notes_app:</h2>";
    echo "<ul>";
    $collections = $db->listCollections();
    foreach ($collections as $collection) {
        echo "<li>" . $collection->getName() . "</li>";
    }
    echo "</ul>";
    
    // Create the users collection if it doesn't exist
    if (!in_array('users', iterator_to_array($db->listCollectionNames()))) {
        echo "<p>Creating 'users' collection...</p>";
        $db->createCollection('users');
        echo "<p style='color: green;'>Successfully created 'users' collection!</p>";
    } else {
        echo "<p style='color: green;'>'users' collection already exists.</p>";
    }
    
    // Create the notes collection if it doesn't exist
    if (!in_array('notes', iterator_to_array($db->listCollectionNames()))) {
        echo "<p>Creating 'notes' collection...</p>";
        $db->createCollection('notes');
        echo "<p style='color: green;'>Successfully created 'notes' collection!</p>";
    } else {
        echo "<p style='color: green;'>'notes' collection already exists.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
    
    // Show more details about the MongoDB setup
    echo "<h2>MongoDB Configuration Details:</h2>";
    echo "<pre>";
    $config = include(__DIR__ . '/api/config/db_config.php');
    print_r($config);
    echo "</pre>";
    
    echo "<h2>PHP Environment:</h2>";
    echo "<pre>";
    echo "PHP Version: " . phpversion() . "\n";
    echo "Loaded Extensions:\n";
    print_r(get_loaded_extensions());
    echo "</pre>";
}
?>
