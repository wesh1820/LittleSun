<?php
require_once './classes/db.class.php';
require_once './classes/Task.class.php';

$db = new Database($dbHost, $dbUsername, $dbPassword, $dbName);
$conn = $db->getConnection();

if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $taskManager = new Task($conn);
    error_log("Fetching tasks for user_id: $userId");
    $tasks = $taskManager->getUserTasksById($userId);

    if ($tasks === false) {
        error_log("No tasks found for user_id: $userId or query error.");
        echo json_encode(["error" => "No tasks found or query error."]);
    } else {
        echo json_encode($tasks);
    }
} else {
    error_log("No user_id provided");
    echo json_encode(["error" => "No user_id provided"]);
}
?>
