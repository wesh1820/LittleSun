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

$userManager = new User($conn);
$result_users = $userManager->getUsers();

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
    <title>Add Hub user</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="hamburger-icon">
    <i class="fas fa-bars"></i>
</div>
<div class="sidebar">
    <div class="logo-sidebar">
        <img src="../LittleSun/css/images/Logo.svg" alt="Logo">
    </div>
    <?php include 'sidebar.php'; ?>
</div>
<div class="container">   
    <?php
    if ($result_users->num_rows > 0) {
        echo "<h2>Hub users</h2>";
        echo "<table>";
        echo "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Actions</th></tr>";
        while ($row_user = $result_users->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row_user['firstname']}</td>";
            echo "<td>{$row_user['lastname']}</td>";
            echo "<td>{$row_user['email']}</td>";
            echo "<td>";
            echo "<a href='edit_user.php?id={$row_user['id']}'>Edit</a> | ";
            echo "<a href='delete_user.php?id={$row_user['id']}'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hub users found.</p>";
    }
    ?>
</div>

<?php if ($user_role === 'admin') : ?>
    <a class="add-button" href="#">add hub user</a>
<?php endif; ?>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="popup-content"></div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
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
