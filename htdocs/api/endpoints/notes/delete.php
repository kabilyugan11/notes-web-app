<?php
require_once '../classes/Database.php';
require_once '../classes/Note.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$note = new Note($db);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id)) {
        $note->id = $data->id;

        if ($note->delete()) {
            echo json_encode(["message" => "Note was deleted."]);
        } else {
            echo json_encode(["message" => "Unable to delete note."]);
        }
    } else {
        echo json_encode(["message" => "Note ID is required."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method."]);
}
?>