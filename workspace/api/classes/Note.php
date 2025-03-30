<?php

class Note {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function create($title, $content, $userId) {
        $query = "INSERT INTO notes (title, content, user_id) VALUES (:title, :content, :user_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function read($userId) {
        $query = "SELECT * FROM notes WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $content) {
        $query = "UPDATE notes SET title = :title, content = :content WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM notes WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}