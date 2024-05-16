<?php
require_once './classes/user.class.php';
require_once './classes/db.class.php';
require_once './classes/SessionManager.class.php';

$email = SessionManager::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
SessionManager::setSession('firstname', $user->getFirstName($email));

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2><i class="fas fa-columns"></i></h2>
        <?php
        if ($user_role === 'admin') {
            echo '<a href="manager.php"><i class="fas fa-user">Managers</i></a>';
            echo '<a href="hub_location.php"><i class="fas fa-map-marker-alt">Locations</i></a>';
            echo '<a href="tasks.php"><i class="fas fa-tasks">Tasks</i></a>';
        } elseif ($user_role === 'manager') {
            echo '<a href="user.php"><i class="fa fa-user">Users</i></a>';
            echo '<a href="user_tasks.php"><i class="fa fa-tasks"> tasks</i></a>';
            echo '<a href="calender.php"><i class="fa fa-calendar"> calender</i></a>';
        } elseif ($user_role === 'user') {
            echo '<a href="clockin.php"><i class="fas fa-tasks">Clockin</i></a>';
            echo '<a href="taskasuser.php"><i class="fas fa-tasks">Tasks</i></a>';
        }
        ?>
        <a href="logout.php"><i class="fas fa-sign-out-alt">Logout</i></a>
    </div>
    <div class="content">
        <h2> Logged in as: <?php echo SessionManager::getSession('firstname'); ?> (<?php echo $user_role; ?>)</h2>
    </div>
</div>
</body>
</html>
?>