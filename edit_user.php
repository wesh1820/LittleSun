<?php
// edit.php

require_once './classes/User.class.php';
require_once './classes/db.class.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userid'])) {
    // Retrieve user ID from POST data
    $userId = $_POST['userid'];

    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Create a new user instance
    $user = new User($db->getConnection());

    // Update user information
    $updated = $user->updateUser($userId, $firstname, $lastname, $email, $password);

    if ($updated) {
        // Redirect to users.php after successful update
        header("Location: user.php");
        exit();
    } else {
        // Handle update error
        echo "Error updating user.";
    }
} else {
    // Redirect to users.php if userid is not set
    header("Location: user.php");
    exit();
}
?>
