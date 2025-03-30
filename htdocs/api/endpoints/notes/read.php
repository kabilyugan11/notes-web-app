<?php
require_once '../classes/Database.php';
require_once '../classes/Note.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : die(json_encode(["message" => "User ID not provided."]));

$stmt = $note->read($userId);
$num = $stmt->rowCount();

if($num > 0) {
    $notes_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $note_item = array(
            "id" => $id,
            "title" => $title,
            "content" => $content,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );
        array_push($notes_arr, $note_item);
    }
    echo json_encode($notes_arr);
} else {
    echo json_encode(array("message" => "No notes found."));
}
?>