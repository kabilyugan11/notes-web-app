<?php
// This script will symlink or copy the API files from workspace to htdocs

echo "<h1>API Setup Script</h1>";

// Define source and destination directories
$sourceDir = __DIR__ . '/../workspace/api';
$destDir = __DIR__ . '/api';

if (!file_exists($sourceDir)) {
    die("<p style='color:red'>Error: Source API directory not found at $sourceDir</p>");
}

// Create the api directory in htdocs if it doesn't exist
if (!file_exists($destDir)) {
    echo "<p>Creating API directory at $destDir</p>";
    mkdir($destDir, 0755, true);
} else {
    // Clean up existing directory
    echo "<p>Cleaning up existing API directory</p>";
    system("rm -rf $destDir");
    mkdir($destDir, 0755, true);
}

// Function to recursively copy files
function recursiveCopy($source, $dest) {
    // Create destination directory if it doesn't exist
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }
    
    // Open the source directory
    $dir = opendir($source);
    
    // Loop through the files in source directory
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            if (is_dir($source . '/' . $file)) {
                // If it's a directory, recursively copy it
                recursiveCopy($source . '/' . $file, $dest . '/' . $file);
            } else {
                // If it's a file, copy it
                copy($source . '/' . $file, $dest . '/' . $file);
                echo "<p>Copied: " . $source . '/' . $file . " to " . $dest . '/' . $file . "</p>";
            }
        }
    }
    
    closedir($dir);
}

// Copy the API files
echo "<p>Copying API files from $sourceDir to $destDir</p>";
recursiveCopy($sourceDir, $destDir);

// Copy vendor directory for MongoDB
if (is_dir(__DIR__ . '/../workspace/vendor') && !is_dir(__DIR__ . '/vendor')) {
    echo "<p>Copying vendor directory for MongoDB</p>";
    system("cp -r " . __DIR__ . "/../workspace/vendor " . __DIR__ . "/vendor");
}

echo "<h2>API Setup Complete</h2>";
echo "<p>API files have been copied to the htdocs directory.</p>";
echo "<p><a href='api_status.php'>Click here to check API status</a></p>";
?>
