<?php
require_once './classes/User.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $manager_id = $_GET['id'];
    $userManager = new User($db->getConnection());
    $manager_info = $userManager->getManagerById($manager_id); 

    if($manager_info) {
        $firstname = $manager_info['firstname'];
        $lastname = $manager_info['lastname'];
        $email = $manager_info['email'];

        if(isset($_POST['submit'])) {
            $updated_firstname = $_POST['firstname'];
            $updated_lastname = $_POST['lastname'];
            $updated_email = $_POST['email'];
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if($new_password === $confirm_password) {
                if(!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                } else {
                    $hashed_password = $manager_info['password'];
                }

                $result = $userManager->updateManager($manager_id, $updated_firstname, $updated_lastname, $updated_email, $hashed_password); // Assuming a method updateManager exists in User class and it accepts the password parameter

                if($result) {

                    echo "<script>alert('Manager information updated successfully');</script>";
                    echo "<script>window.location.href = 'manager.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Failed to update manager information');</script>";
                }
            } else {
                echo "<script>alert('Passwords do not match');</script>";
            }
        }
    } else {

        echo "<script>alert('Manager not found');</script>";
        echo "<script>window.location.href = 'manager.php';</script>";
        exit;
    }
} else {

    echo "<script>alert('Manager ID not provided');</script>";
    echo "<script>window.location.href = 'managers.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h2>Edit Manager</h2>
        <form method="POST">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <button type="submit" name="submit" class="view-button">Update</button>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/script.js"></script>
<script>
        $(document).ready(function(){
            $('#close-popup').click(function(){
                $('#tasks-popup').hide();
            });
        });
$(document).ready(function() {
    $(".hamburger-icon").click(function() {
        $(".sidebar").toggleClass("sidebar-open");
    });
    $(".add-button").click(function() {
        $("#popup-content").load("add_user.php");
        $("#myModal").css("display", "block");
    });
    $(".close, .modal").click(function() {
        $("#myModal").css("display", "none");
    });
    $(".modal-content").click(function(event) {
        event.stopPropagation();
    });
});
</script>
</body>
</html>
