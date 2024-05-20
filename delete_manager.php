<?php
require_once './classes/User.class.php'; // Include the UserManager class
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

// Check if the manager ID is provided in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $manager_id = $_GET['id'];
    
    // Delete the manager from the database
    $userManager = new User($db->getConnection());
    $result = $userManager->deleteManager($manager_id); // Assuming a method deleteManager exists in User class

    if($result) {
        // Manager deleted successfully
        echo "<script>alert('Manager deleted successfully');</script>";
    } else {
        // Failed to delete manager
        echo "<script>alert('Failed to delete manager');</script>";
    }

    // Redirect to the page where managers are listed
    echo "<script>window.location.href = 'manager.php';</script>";
    exit;
} else {
    // Manager ID not provided in URL
    echo "<script>alert('Manager ID not provided');</script>";
    // Redirect to the page where managers are listed
    echo "<script>window.location.href = 'manager.php';</script>";
    exit;
}
?>
