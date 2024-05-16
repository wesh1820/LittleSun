<?php
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/SessionManager.class.php';
require_once './classes/User.class.php';

$email = SessionManager::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
SessionManager::setSession('firstname', $user->getFirstName($email));

$db->closeConnection();

$taskManager = new Task($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_task'])) {
        $taskName = $_POST['task_name'];
        $taskManager->addTask($taskName);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['delete_task'])) {
        $taskId = $_POST['task_id'];
        $taskManager->deleteTask($taskId);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

$tasks = $taskManager->getAllTasks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tasks</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>All Tasks</h2>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="task_name">New Task:</label>
        <input type="text" id="task_name" name="task_name" required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <?php if (!empty($tasks)): ?>
        <table>
            <tr>
                <th>Task Name</th>
                <th>Action</th>
            </tr>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['TaskName']; ?></td>
                    <td>
                        <a href="edit_task.php?id=<?php echo $task['TaskID']; ?>">Edit</a>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline-block;">
                            <input type="hidden" name="task_id" value="<?php echo $task['TaskID']; ?>">
                            <button type="submit" name="delete_task">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No tasks found.</p>
    <?php endif; ?>
</div>
</body>
</html>
