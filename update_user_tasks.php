<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';
require_once './classes/Task.class.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tasks']) && isset($_GET['userid']) && is_numeric($_GET['userid'])) {
    $userid = intval($_GET['userid']);
    $tasks = $_POST['tasks']; // Array of task IDs

    // Create a new user instance
    $user = new User($db->getConnection());

    // Update user tasks in the database
    $user->updateUserTasks($userid, $tasks);

    // Redirect back to the user page
    header("Location: user.php");
    exit();
} else {
    // If form is not submitted or parameters are missing, redirect to the users page
    header("Location: user.php");
    exit();
}
?>
