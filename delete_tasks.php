<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

if (!isset($_GET['id'])) {
    header("Location: manager.php");
    exit();
}

$id = $_GET['id'];
$taskManager = new Task($db);
$taskManager->deleteTask($id);
$db->closeConnection();
?>
