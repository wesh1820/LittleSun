<?php
session_start();
require_once 'config.php';

// Fetch hub managers from the database
$sql_managers = "SELECT * FROM users WHERE typeOfUser = 'manager'";
$result_managers = $conn->query($sql_managers);

// Define $user_role before including sidebar.php
$user_role = ""; // Default value, change this according to your logic
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql_role = "SELECT typeOfUser FROM users WHERE email = '$email'";
    $result_role = $conn->query($sql_role);
    if ($result_role && $result_role->num_rows > 0) {
        $row_role = $result_role->fetch_assoc();
        $user_role = $row_role['typeOfUser'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hub Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">    
    <?php
    if ($result_managers->num_rows > 0) {
        // Display the hub managers in a table
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
        // If no hub managers were found, display a message
        echo "<p>No hub managers found.</p>";
    }
    ?>
</div>
<div class="sidebar">
    <?php
    // Include sidebar.php after defining $user_role
    include 'sidebar.php';
    ?>
</div>
<!-- This is the link to trigger the popup -->
<!-- This is the link to trigger the popup -->
<a class="add-button" href="#">Voeg manager toe</a>

<!-- This is the modal popup -->
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
    // Open the popup when the link is clicked
    $(".add-button").click(function() {
        // Load the content of "add_manager.php" into the modal
        $("#popup-content").load("add_manager.php");
        // Show the modal
        $("#myModal").css("display", "block");
    });

    // Close the popup when the close button or outside the modal is clicked
    $(".close, .modal").click(function() {
        $("#myModal").css("display", "none");
    });

    // Prevent the modal from closing when clicking inside the modal content
    $(".modal-content").click(function(event) {
        event.stopPropagation();
    });
});
</script>



</html>
