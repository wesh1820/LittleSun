<?php
require_once './classes/User.class.php'; // Include the UserManager class
require_once './classes/db.class.php';
require_once './classes/SessionManager.class.php';

$email = SessionManager::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
SessionManager::setSession('firstname', $user->getFirstName($email));

$db->closeConnection();

$userManager = new User($conn);
$result_managers = $userManager->getManagers();

$user_role = "";
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $user_role = $userManager->getUserRole($email);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Managers</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">    
    <?php
    if ($result_managers->num_rows > 0) {

        echo "<h2>Hub Managers</h2>";
        echo "<table>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Actions</th></tr>";
        while ($row_manager = $result_managers->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row_manager['firstname']}</td>";
            echo "<td>{$row_manager['lastname']}</td>";
            echo "<td>{$row_manager['email']}</td>";
            echo "<td>";
            echo "<a href='edit_manager.php?id={$row_manager['id']}'>Edit</a> | ";
            echo "<a href='delete_manager.php?id={$row_manager['id']}'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {

        echo "<p>No hub managers found.</p>";
    }
    ?>
</div>
<div class="sidebar">
    <?php

    include 'sidebar.php';
    ?>
</div>

<?php if ($user_role === 'admin') : ?>
    <a class="add-button" href="#">Add Hub Manager</a>
<?php endif; ?>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="popup-content"></div>
  </div>
</div>


</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
   
    $(".add-button").click(function() {
       
        $("#popup-content").load("add_manager.php");
        
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

</html>
