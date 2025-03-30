<?php
// Require Composer's autoloader to load MongoDB\Client class
require_once __DIR__ . '/../../vendor/autoload.php';

class Database {
    private $connection;
    private $dbname;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            // Load configuration
            $config = include(__DIR__ . '/../config/db_config.php');
            $this->dbname = $config['dbname'];
            
            // Log connection attempt
            $logFile = __DIR__ . '/db_log.txt';
            file_put_contents($logFile, "Connecting to MongoDB: " . $config['dsn'] . ", DB: " . $this->dbname . "\n", FILE_APPEND);
            
            // Create MongoDB client with required options
            $this->connection = new MongoDB\Client(
                $config['dsn'],
                [],
                ['typeMap' => ['root' => 'array', 'document' => 'array']]
            );
            
            // Test connection by listing databases
            $this->connection->listDatabases();
            
            file_put_contents($logFile, "MongoDB connection successful\n", FILE_APPEND);
            return true;
        } catch (Exception $e) {
            file_put_contents($logFile, "MongoDB connection error: " . $e->getMessage() . "\n", FILE_APPEND);
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getDbName() {
        return $this->dbname;
    }

    public function query($collection, $filter = [], $options = []) {
        $db = $this->connection->selectDatabase($this->dbname);
        return $db->$collection->find($filter, $options);
    }

    public function insert($collection, $document) {
        $db = $this->connection->selectDatabase($this->dbname);
        return $db->$collection->insertOne($document);
    }

    public function update($collection, $filter, $update) {
        $db = $this->connection->selectDatabase($this->dbname);
        return $db->$collection->updateOne($filter, $update);
    }

    public function delete($collection, $filter) {
        $db = $this->connection->selectDatabase($this->dbname);
        return $db->$collection->deleteOne($filter);
    }
}
?>