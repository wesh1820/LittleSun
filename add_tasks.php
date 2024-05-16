<?php
require_once 'config.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name']; // Assuming your form has a field named task_name

    // Insert task into the database
    $sql_insert_task = "INSERT INTO tasks (TaskName) VALUES (?)";
    $stmt_insert_task = $conn->prepare($sql_insert_task);
    $stmt_insert_task->bind_param("s", $task_name);

    if ($stmt_insert_task->execute()) {
        // Redirect after successful insertion
        header("Location: index.php"); // Replace 'index.php' with the actual name of your index page
        exit();
    } else {
        echo "Error: " . $sql_insert_task . "<br>" . $conn->error;
    }

    // Close prepared statement
    $stmt_insert_task->close();
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
