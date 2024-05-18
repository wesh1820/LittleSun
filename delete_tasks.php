<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

// Check if ID is set
if (!isset($_GET['id'])) {
    header("Location: manager.php");
    exit();
}

$id = $_GET['id'];

// Initialize TaskManager and delete task
$taskManager = new Task($db);
$taskManager->deleteTask($id);

// Close database connection
$db->closeConnection();
?>
