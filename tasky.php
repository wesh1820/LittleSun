<?php
require_once './classes/User.class.php';
require_once './classes/db.class.php';

$user = new User($db->getConnection());
$loggedInUserId = $_SESSION['user_id']; 
$loggedInUserLocationId = $user->getLocationId($loggedInUserId);
$users = $user->getUsersByLocation($loggedInUserLocationId);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Users and Their Tasks</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
<div class="container">
    <h2>Usertasks</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $users->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['firstname'] . " " . $row['lastname'] . "</td>";
                echo "<td>";
                echo "<button onclick='GetTasks(" . $row['id'] . ")' class='view-button'>Assign tasks</button>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="tasks-popup">
    <div id="tasks-popup-content">
    </div>
    <span id="close-popup">&times;</span>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/script.js"></script>
</body>
</html>


