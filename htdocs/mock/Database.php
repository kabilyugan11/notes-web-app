<?php
// This is a mock Database class that simulates MongoDB functionality
// It stores data in files instead of a real database for testing purposes

// Define the MongoDB namespace and Client class if they don't exist
if (!class_exists('MongoDB\Client')) {
    class_alias('Database', 'MongoDB\Client');
}

// Mock classes for MongoDB\BSON types
class UTCDateTime {
    public function __construct() {
        $this->date = date('c');
    }
    
    public function __toString() {
        return $this->date;
    }
}

// Using proper function instead of namespace declaration
function create_mongodb_bson_utcdatetime_alias() {
    if (!class_exists('MongoDB\BSON\UTCDateTime')) {
        class_alias('UTCDateTime', 'MongoDB\BSON\UTCDateTime');
    }
}
create_mongodb_bson_utcdatetime_alias();

class Database {
    private $dataDir;
    private $dbname = 'notes_app';

    public function __construct() {
        // Create a data directory for our mock database
        $this->dataDir = __DIR__ . '/data';
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        // Create collections subdirectories
        $collectionsDir = $this->dataDir . '/' . $this->dbname;
        if (!file_exists($collectionsDir)) {
            mkdir($collectionsDir, 0755, true);
        }
        
        // Initialize users collection
        $usersDir = $collectionsDir . '/users';
        if (!file_exists($usersDir)) {
            mkdir($usersDir, 0755, true);
        }
        
        // Initialize notes collection
        $notesDir = $collectionsDir . '/notes';
        if (!file_exists($notesDir)) {
            mkdir($notesDir, 0755, true);
        }
    }

    public function getConnection() {
        return $this;
    }

    public function getDbName() {
        return $this->dbname;
    }
    
    public function selectDatabase($dbname) {
        // Return a collection proxy object
        return new CollectionProxy($this->dataDir, $dbname);
    }
}

// A class to simulate MongoDB collections
class CollectionProxy {
    private $dataDir;
    private $dbname;
    
    public function __construct($dataDir, $dbname) {
        $this->dataDir = $dataDir;
        $this->dbname = $dbname;
    }
    
    public function __get($name) {
        // Return a collection when accessed like $db->users
        return new Collection($this->dataDir, $this->dbname, $name);
    }
    
    public function listCollections() {
        $collections = [];
        $dbDir = $this->dataDir . '/' . $this->dbname;
        if (file_exists($dbDir)) {
            foreach (scandir($dbDir) as $item) {
                if ($item != '.' && $item != '..' && is_dir($dbDir . '/' . $item)) {
                    $collections[] = (object)['name' => $item];
                }
            }
        }
        return $collections;
    }
    
    public function listCollectionNames() {
        $names = [];
        foreach ($this->listCollections() as $collection) {
            $names[] = $collection->name;
        }
        return new ArrayIterator($names);
    }
    
    public function createCollection($name) {
        $collectionDir = $this->dataDir . '/' . $this->dbname . '/' . $name;
        if (!file_exists($collectionDir)) {
            mkdir($collectionDir, 0755, true);
        }
        return true;
    }
}

// A class to simulate MongoDB collection operations
class Collection {
    private $dataDir;
    private $dbname;
    private $name;
    
    public function __construct($dataDir, $dbname, $name) {
        $this->dataDir = $dataDir;
        $this->dbname = $dbname;
        $this->name = $name;
    }
    
    private function getCollectionPath() {
        return $this->dataDir . '/' . $this->dbname . '/' . $this->name;
    }
    
    public function insertOne($document) {
        // Generate a unique ID
        $id = uniqid();
        $document['_id'] = $id;
        
        // Save to file
        $filePath = $this->getCollectionPath() . '/' . $id . '.json';
        file_put_contents($filePath, json_encode($document));
        
        // Return result object
        return new InsertResult($id);
    }
    
    public function findOne($filter) {
        // If we're searching by ID
        if (isset($filter['_id'])) {
            $filePath = $this->getCollectionPath() . '/' . $filter['_id'] . '.json';
            if (file_exists($filePath)) {
                return json_decode(file_get_contents($filePath), true);
            }
            return null;
        }
        
        // If we're searching by other criteria
        $documents = $this->findAll();
        foreach ($documents as $document) {
            $match = true;
            
            // Handle $or operator
            if (isset($filter['$or'])) {
                $match = false;
                foreach ($filter['$or'] as $orCondition) {
                    $orMatch = true;
                    foreach ($orCondition as $field => $value) {
                        if (!isset($document[$field]) || $document[$field] !== $value) {
                            $orMatch = false;
                            break;
                        }
                    }
                    if ($orMatch) {
                        $match = true;
                        break;
                    }
                }
            } else {
                // Regular criteria matching
                foreach ($filter as $field => $value) {
                    if (!isset($document[$field]) || $document[$field] !== $value) {
                        $match = false;
                        break;
                    }
                }
            }
            
            if ($match) {
                return $document;
            }
        }
        
        return null;
    }
    
    private function findAll() {
        $documents = [];
        $dir = $this->getCollectionPath();
        if (file_exists($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'json') {
                    $documents[] = json_decode(file_get_contents($dir . '/' . $file), true);
                }
            }
        }
        return $documents;
    }
    
    public function find($filter = [], $options = []) {
        // Implement find functionality
        $documents = $this->findAll();
        $results = [];
        
        foreach ($documents as $document) {
            $match = true;
            
            // Simple filter implementation
            foreach ($filter as $field => $value) {
                if (!isset($document[$field]) || $document[$field] !== $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                $results[] = $document;
            }
        }
        
        return $results;
    }
}

// Result objects to mimic MongoDB results
class InsertResult {
    private $id;
    
    public function __construct($id) {
        $this->id = $id;
    }
    
    public function getInsertedCount() {
        return 1;
    }
    
    public function getInsertedId() {
        return $this->id;
    }
}
