<?php
require_once '../classes/Database.php';
require_once '../classes/Note.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);

$data = json_decode(file_get_contents("php://input"));

if(isset($data->id) && isset($data->title) && isset($data->content)) {
    $note->id = $data->id;
    $note->title = $data->title;
    $note->content = $data->content;

    if($note->update()) {
        echo json_encode(array("message" => "Note updated successfully."));
    } else {
        echo json_encode(array("message" => "Unable to update note."));
    }
} else {
    echo json_encode(array("message" => "Invalid input."));
}
?>