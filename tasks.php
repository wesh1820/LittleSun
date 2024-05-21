<?php
    require_once './classes/User.class.php';
    require_once './classes/db.class.php';
    require_once './classes/Task.class.php';

    if (isset($_GET['userid']) && is_numeric($_GET['userid'])) {
        $userid = intval($_GET['userid']);
        $user = new User($db->getConnection());
        $userData = $user->getUserById($userid);
        $tasks = $user->getAllTasksWithUserCheck($userid);
    } 
    ?><!DOCTYPE html>
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
    if ($userData) {
        echo "<h2>Tasks for " . $userData['firstname'] . " " . $userData['lastname'] . "</h2>";
        if ($tasks) {
            echo "<form method='post' action='update_user_tasks.php?userid=$userid'>";
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
