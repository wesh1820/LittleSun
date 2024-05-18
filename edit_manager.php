<?php
require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

// Check if user ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: manager.php");
    exit();
}

$id = $_GET['id'];

$userManager = new User($conn);

// Get the user details
$row = $userManager->getUserById($id);

if (!$row) {
    header("Location: manager.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($userManager->updateUser($id, $firstname, $lastname, $email, $password)) {
        header("Location: manager.php");
        exit();
    } else {
        echo "Error updating user.";
    }
}

$conn->close();
?>
