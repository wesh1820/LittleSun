<?php
require_once './classes/Task.class.php';
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';
require './sidebar.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$conn = $db->getConnection();
$taskManager = new Task($conn);
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $task = $taskManager->getTaskById($taskId);

    if (!$task) {
        echo "Taak niet gevonden.";
        exit();
    }
} else {
    echo "Geen taak-ID opgegeven.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    $taskName = $_POST['task_name'];
    $taskId = $_POST['task_id'];

    $taskManager->updateTask($taskId, $taskName);
    header("Location: task.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>Edit Task</h2>
    <form action="edit_task.php?id=<?php echo htmlspecialchars($taskId); ?>" method="post">
        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task['TaskName']); ?>" required>
        <button class="view-button" type="submit" name="update_task">Update Task</button>
    </form>
</div>
</body>
</html>
