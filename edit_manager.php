<?php
require_once './classes/User.class.php'; // Include the UserManager class
require_once './classes/db.class.php';
require_once './classes/Session.class.php';

// Check if the manager ID is provided in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $manager_id = $_GET['id'];
    
    // Retrieve the manager's information from the database
    $userManager = new User($db->getConnection());
    $manager_info = $userManager->getManagerById($manager_id); // Assuming a method getManagerById exists in User class

    if($manager_info) {
        // Manager information retrieved successfully
        $firstname = $manager_info['firstname'];
        $lastname = $manager_info['lastname'];
        $email = $manager_info['email'];

        // Check if form is submitted for updating manager information
        if(isset($_POST['submit'])) {
            // Retrieve updated information from form
            $updated_firstname = $_POST['firstname'];
            $updated_lastname = $_POST['lastname'];
            $updated_email = $_POST['email'];

            // Update manager information in the database
            $result = $userManager->updateManager($manager_id, $updated_firstname, $updated_lastname, $updated_email); // Assuming a method updateManager exists in User class

            if($result) {
                // Manager information updated successfully
                echo "<script>alert('Manager information updated successfully');</script>";
                // Redirect to the page where managers are listed
                echo "<script>window.location.href = 'manager.php';</script>";
                exit;
            } else {
                echo "<script>alert('Failed to update manager information');</script>";
            }
        }
    } else {
        // Manager not found
        echo "<script>alert('Manager not found');</script>";
        // Redirect to the page where managers are listed
        echo "<script>window.location.href = 'manager.php';</script>";
        exit;
    }
} else {
    // Manager ID not provided in URL
    echo "<script>alert('Manager ID not provided');</script>";
    // Redirect to the page where managers are listed
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
    <div class="container">
        <h2>Edit Manager</h2>
        <form method="POST">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            <button type="submit" name="submit" class="view-button">Update</button>
        </form>
    </div>
</body>
</html>
