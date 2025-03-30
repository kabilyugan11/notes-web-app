<?php

class User {
    private $db;
    private $logFile;

    public function __construct($database = null) {
        // Set up logging
        $this->logFile = __DIR__ . '/user_log.txt';
        file_put_contents($this->logFile, "User class initialized: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        
        if ($database) {
            $this->db = $database;
            file_put_contents($this->logFile, "Database provided to constructor\n", FILE_APPEND);
        } else {
            file_put_contents($this->logFile, "Creating new database connection\n", FILE_APPEND);
            require_once 'Database.php';
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    public function register($username, $email, $password) {
        try {
            // Log registration details
            file_put_contents($this->logFile, "Register attempt for: $username, $email\n", FILE_APPEND);
            
            // Confirm we have a database connection
            if (!$this->db) {
                file_put_contents($this->logFile, "ERROR: No database connection\n", FILE_APPEND);
                throw new Exception("No database connection available");
            }
            
            // Get the users collection
            file_put_contents($this->logFile, "Selecting database\n", FILE_APPEND);
            $database = $this->db->selectDatabase('notes_app');
            
            file_put_contents($this->logFile, "Getting users collection\n", FILE_APPEND);
            $usersCollection = $database->users;
            
            file_put_contents($this->logFile, "Database selected, collection accessed\n", FILE_APPEND);
            
            // Check if username or email already exists
            file_put_contents($this->logFile, "Checking for existing user\n", FILE_APPEND);
            $existingUser = $usersCollection->findOne([
                '$or' => [
                    ['username' => $username],
                    ['email' => $email]
                ]
            ]);
            
            if ($existingUser) {
                file_put_contents($this->logFile, "User already exists\n", FILE_APPEND);
                return false; // User already exists
            }
            
            // Insert the new user
            file_put_contents($this->logFile, "Inserting new user\n", FILE_APPEND);
            $result = $usersCollection->insertOne([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'createdAt' => new MongoDB\BSON\UTCDateTime(),
                'updatedAt' => new MongoDB\BSON\UTCDateTime()
            ]);
            
            $success = $result->getInsertedCount() > 0;
            file_put_contents($this->logFile, "Insert result: " . ($success ? "Success" : "Failed") . "\n", FILE_APPEND);
            
            return $success;
        } catch (Exception $e) {
            file_put_contents($this->logFile, "Registration error: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($this->logFile, "Error trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
            throw $e; // Rethrow to handle in the calling code
        }
    }

    public function login($usernameOrEmail, $password) {
        try {
            // Get the users collection
            $db = $this->db->selectDatabase('notes_app');
            $usersCollection = $db->users;
            
            // Try to find user by email or username
            $user = $usersCollection->findOne([
                '$or' => [
                    ['email' => $usernameOrEmail],
                    ['username' => $usernameOrEmail]
                ]
            ]);
            
            if ($user && password_verify($password, $user['password'])) {
                return [
                    'id' => (string)$user['_id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return true;
    }

    public function getUserById($id) {
        try {
            // Get the users collection
            $db = $this->db->selectDatabase('notes_app');
            $usersCollection = $db->users;
            
            $user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
            
            if ($user) {
                return [
                    'id' => (string)$user['_id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
}
?>