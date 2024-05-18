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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .pop-up-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }   

        .form-edit label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-edit input[type="text"], .form-edit input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-edit input[type="submit"] {
            margin-top: 10px;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="pop-up-container">    
    <h2>Edit Manager</h2>
    <form method="post">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" required>
        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" required>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo $row['email']; ?>" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter new password">
        <input type="submit" value="Update">
    </form>
</div>
</body>
</html>

<?php

$stmt_select_manager->close();
$conn->close();
?>