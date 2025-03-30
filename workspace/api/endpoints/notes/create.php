<?php
require_once '../classes/Database.php';
require_once '../classes/Note.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);

$data = json_decode(file_get_contents("php://input"));

if (isset($data->title) && isset($data->content)) {
    $note->title = $data->title;
    $note->content = $data->content;
    $note->user_id = $data->user_id; // Assuming user_id is sent in the request

    if ($note->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Note was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create note."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create note. Data is incomplete."));
}
?>