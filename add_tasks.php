<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name'];

    $taskManager = new Task($conn);

    if ($taskManager->addTask($task_name)) {
        header("Location: tasks.php"); 
        exit();
    } else {
        echo "Error: Failed to add task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Add Task</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required><br>

            <input type="submit" value="Add Task">
        </form>
    </div>
</body>
</html>
