<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Return a success response
    echo json_encode(["message" => "Logout successful."]);
} else {
    // Return an error response if the user is not logged in
    echo json_encode(["message" => "No user is logged in."]);
}
?>