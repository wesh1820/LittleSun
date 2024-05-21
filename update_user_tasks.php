<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';
require_once './classes/Task.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tasks']) && isset($_GET['userid']) && is_numeric($_GET['userid'])) {
    $userid = intval($_GET['userid']);
    $tasks = $_POST['tasks']; 
    $user = new User($db->getConnection());
    $user->updateUserTasks($userid, $tasks);

    header("Location: user.php");
    exit();
} else {

    header("Location: user.php");
    exit();
}
?>
