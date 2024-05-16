<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Document</title>
</head>
<body>
<div class="container">
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
    include 'sidebar.php';
    

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userTasks = new Task($conn);
        $userTasks->getUserTasks($user_id);
    } else {
        echo "You are not logged in!";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
