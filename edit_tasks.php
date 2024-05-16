<?php
require_once 'config.php';

// Check if task ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: tasks.php");
    exit();
}

// Get the task ID from the URL
$id = $_GET['id'];

// Prepare and execute the query to select the task
$sql_select_task = "SELECT TaskID, TaskName FROM Tasks WHERE TaskID = ?";
$stmt_select_task = $conn->prepare($sql_select_task);
$stmt_select_task->bind_param("i", $id);
$stmt_select_task->execute();
$result_task = $stmt_select_task->get_result();

// Check if a task with the given ID exists
if ($result_task->num_rows !== 1) {
    header("Location: tasks.php");
    exit();
}

// Fetch the task details
$row = $result_task->fetch_assoc();

// Close the prepared statement for selecting the task
$stmt_select_task->close();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $task_name = $_POST['task_name'];

    // Prepare and execute the query to update the task
    $sql_update_task = "UPDATE Tasks SET TaskName = ? WHERE TaskID = ?";
    $stmt_update_task = $conn->prepare($sql_update_task);
    $stmt_update_task->bind_param("si", $task_name, $id);

    if ($stmt_update_task->execute()) {
        // Redirect to the manager page after successful update
        header("Location: tasks.php");
        exit();
    } else {
        // Handle update error
        echo "Error: " . $sql_update_task . "<br>" . $conn->error;
    }

    // Close the prepared statement for updating the task
    $stmt_update_task->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">    
        <h2>Edit Task</h2>
        <form method="post">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($row['TaskName']); ?>" required>
            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>

<?php

$conn->close();
?>
