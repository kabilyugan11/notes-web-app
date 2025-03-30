<?php
// Check if MongoDB extension is installed
if (class_exists('MongoDB\Client')) {
    echo "MongoDB extension is installed correctly.";
    
    try {
        // Try to connect to MongoDB
        $config = include(__DIR__ . '/api/config/db_config.php');
        $client = new MongoDB\Client($config['dsn']);
        
        // List databases as a connection test
        $dbs = [];
        foreach ($client->listDatabases() as $db) {
            $dbs[] = $db->getName();
        }
        
        echo "<br>Successfully connected to MongoDB server.<br>";
        echo "Available databases: " . implode(', ', $dbs);
    } catch (Exception $e) {
        echo "<br>Error connecting to MongoDB: " . $e->getMessage();
    }
} else {
    echo "MongoDB extension is NOT installed. Please install the MongoDB PHP extension.";
}

// Output additional PHP information for debugging
echo "<h2>PHP Info</h2>";
echo "<h3>Loaded Extensions</h3>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
?>
