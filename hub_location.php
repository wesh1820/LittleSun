<?php
require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';


$email = Session::getSession('email');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($email);
Session::setSession('firstname', $user->getFirstName($email));

$db->closeConnection();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$locationManager = new Location($conn);
$locations = $locationManager->getLocations();

$user_role = ""; 
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
            <th>Name</th>
            <th>City</th>
            <th>Country</th>
            <th>Action</th>
        </tr>
        <?php

        if (!empty($locations)) {
            foreach ($locations as $location) {
                echo "<tr>";
                echo "<td>{$location['name']}</td>";
                echo "<td>{$location['city']}</td>";
                echo "<td>{$location['country']}</td>";
                echo "<td><a href='edit_location.php?id={$location['id']}'>Edit</a> | <a href='delete_location.php?id={$location['id']}'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hub locations found</td></tr>";
        }
        ?>
    </table>
</div>
<div class="sidebar">
    <?php include 'sidebar.php'; ?>
</div>
<a class="add-button" href="#">Add location</a>

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

        $("#popup-content").load("add_hub_location.php");

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
