<?php
session_start();
require_once 'config.php';

// Fetch hub locations from the database
$sql = "SELECT * FROM locations";
$result = $conn->query($sql);

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
    <title>Hub Locations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">    
    <h2>Hub Locations</h2>
    <table>
        <tr>
            <th>Afbeelding</th>
            <th>Naam</th>
            <th>Stad</th>
            <th>Land</th>
            <th>Actie</th>
        </tr>
        <?php
        // Check if $result is set and not null
        if ($result !== null && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['image']}</td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['city']}</td>";
                echo "<td>{$row['country']}</td>";
                echo "<td><a href='edit_location.php?id={$row['id']}'>Bewerken</a> | <a href='delete_location.php?id={$row['id']}'>Verwijderen</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Geen hublocaties gevonden</td></tr>";
        }
        ?>
    </table>
</div>
<div class="sidebar">
    <?php
    // Include sidebar.php after defining $user_role
    include 'sidebar.php';
    ?>
</div>
<a class="add-button" href="#">Add location</a>

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
        $("#popup-content").load("add_hub_location.php");
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
</div>
</body>
</html>
