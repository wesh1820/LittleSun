<?php
require_once './classes/User.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $manager_id = $_GET['id'];
    $userManager = new User($db->getConnection());
    $result = $userManager->deleteManager($manager_id);

    if($result) {
        echo "<script>alert('Manager deleted successfully');</script>";
    } else {
        echo "<script>alert('Failed to delete manager');</script>";
    }
    echo "<script>window.location.href = 'manager.php';</script>";
    exit;
} else {
    echo "<script>alert('Manager ID not provided');</script>";
    echo "<script>window.location.href = 'manager.php';</script>";
    exit;
}
?>
