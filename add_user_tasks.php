<?php
require_once 'config.php';

$sql_users = "SELECT id, firstname, lastname FROM users WHERE typeOfUser = 'USER'";
$result_users = $conn->query($sql_users);

$sql_tasks = "SELECT TaskID, TaskName FROM tasks";
$result_tasks = $conn->query($sql_tasks);

if ($result_users->num_rows > 0) {
    $user_options = array();
    while ($row_user = $result_users->fetch_assoc()) {
        $user_id = $row_user['id'];
        $user_name = $row_user['firstname'] . ' ' . $row_user['lastname'];
        $user_options[] = "<option value='$user_id'>$user_name</option>";
    }

    $user_options_html = implode('', $user_options);
} else {
    $user_options_html = "<option value=''>No users found</option>";
}

if ($result_tasks->num_rows > 0) {
    $task_options = array();
    while ($row_task = $result_tasks->fetch_assoc()) {
        $task_id = $row_task['TaskID'];
        $task_name = $row_task['TaskName'];
        $task_options[] = "<option value='$task_id'>$task_name</option>";
    }

    $task_options_html = implode('', $task_options);
} else {
    $task_options_html = "<option value=''>No tasks found</option>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_user_id = $_POST['selected_user'];
    $selected_task_id = $_POST['selected_task'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $sql_insert_assignment = "INSERT INTO UserTasks (UserID, TaskID, Date, StartTime, EndTime) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_assignment = $conn->prepare($sql_insert_assignment);
    $stmt_insert_assignment->bind_param("iisss", $selected_user_id, $selected_task_id, $date, $start_time, $end_time);

    if ($stmt_insert_assignment->execute()) {
        header("Location: user_tasks.php");
    } else {
        echo "Error assigning user to task: " . $conn->error;
    }

    $stmt_insert_assignment->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User to Task</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Add User to Task</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="selected_user">Select User:</label>
            <select id="selected_user" name="selected_user" required>
                <?php echo $user_options_html; ?>
            </select><br>

            <label for="selected_task">Select Task:</label>
            <select id="selected_task" name="selected_task" required>
                <?php echo $task_options_html; ?>
            </select><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br>

            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required><br>

            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required><br>

            <input type="submit" value="Assign Task">
        </form>
    </div>
</body>
</html>
