<?php
require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

$id = Session::getSession('id');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($id);
Session::setSession('id', $user->getID($id));

$db->closeConnection();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$locationManager = new Location($conn);

if (isset($_GET['search_query'])) {
    $searchQuery = $_GET['search_query'];
    $locations = $locationManager->searchLocations($searchQuery);
} else {
    $locations = $locationManager->getLocations();
}

$user_role = ""; 
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $sql_role = "SELECT typeOfUser FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql_role);
    if ($stmt) {
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result_role = $stmt->get_result();
        if ($result_role && $result_role->num_rows > 0) {
            $row_role = $result_role->fetch_assoc();
            $user_role = $row_role['typeOfUser'];
        }
    } else {
        echo "Prepare failed: " . $conn->error;
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
        <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
<div class="container">    
    <h2>Hub Locations</h2>

    <form method="GET" action="">
        <input type="text" name="search_query" placeholder="Search locations">
        <button class="view-button" type="submit">Search</button>
    </form>
    
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
                echo "<td><a href='edit_location.php?id={$location['id']}' class='view-button'>Edit</a><a href='delete_location.php?id={$location['id']}' class='view-button'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No locations found</td></tr>";
        }
        ?>
    </table>
</div>
<a class="add-button" href="#">Add</a>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="popup-content"></div>
  </div>
</div>

</body>
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
</html>
