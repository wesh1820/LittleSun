<!DOCTYPE html>
<html>
<head>
    <title>User Tasks</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
    <h1>User Tasks</h1>

    <?php
    require_once './classes/User.class.php';
    require_once './classes/db.class.php';
    require_once './classes/Task.class.php';

    // Check if userid is set and valid
    if (isset($_GET['userid']) && is_numeric($_GET['userid'])) {
        $userid = intval($_GET['userid']);

        // Create a new user instance
        $user = new User($db->getConnection());

        // Get user information
        $userData = $user->getUserById($userid);

        // Get all tasks with indication if the user has the task or not
        $tasks = $user->getAllTasksWithUserCheck($userid);
    } else {
        // If userid is not set or not valid, redirect to the users page
        header("Location: users.php");
        exit();
    }
    ?>

    <?php
    // Show all tasks with a checkbox to indicate which tasks the user has
    if ($userData) {
        echo "<h2>Tasks for " . $userData['firstname'] . " " . $userData['lastname'] . "</h2>";
        if ($tasks) {
            echo "<form method='post' action='update_user_tasks.php?userid=$userid'>"; // Updated form action
            foreach ($tasks as $task) {
                $checked = $task['HasTask'] ? 'checked' : '';
                echo "<input type='checkbox' name='tasks[]' value='" . $task['TaskID'] . "' $checked>" . $task['TaskName'] . "<br>";
            }
            echo "<input type='submit' value='Update Tasks'>";
            echo "</form>";
        } else {
            echo "<p>No tasks found</p>";
        }
    } else {
        echo "<h2>User not found</h2>";
    }
    ?>

    <br>
    </div>
</body>
</html>
