<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: manager.php");
    exit();
}

$id = $_GET['id'];

$sql_delete_task = "DELETE FROM Tasks WHERE TaskID = ?";
$stmt_delete_task = $conn->prepare($sql_delete_task);
$stmt_delete_task->bind_param("i", $id);

if ($stmt_delete_task->execute()) {
    header("Location: tasks.php");
    exit();
} else {

    echo "Error: " . $sql_delete_task . "<br>" . $conn->error;
}

$stmt_delete_task->close();

$conn->close();
?>
