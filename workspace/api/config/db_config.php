<?php
// Database configuration settings for connecting to MongoDB

$mongoDBHost = 'localhost'; // MongoDB server host
$mongoDBPort = '27017'; // MongoDB server port
$mongoDBDatabase = 'notes_app'; // Database name

// Return configuration as an array
return [
    'dsn' => "mongodb://$mongoDBHost:$mongoDBPort",
    'dbname' => $mongoDBDatabase
];
?>