<?php
require_once './classes/Clock.class.php'; 
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php'; 
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

$id = Session::getSession('id');
$user = new User($db->getConnection());
$user_role = $user->getUserRole($id);
Session::setSession('id', $user->getID($id));

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$clockManager = new Clock($db->getConnection());

if(isset($_POST['clock_in'])) {
    $message = $clockManager->clockIn($user_id);
}

if(isset($_POST['clock_out'])) {
    $message = $clockManager->clockOut($user_id);
}

$result = $clockManager->getUserRecords($user_id);
$clocked_in = $clockManager->isClockedIn($user_id);
$db->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Clock In/Out System</title>
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h2>Clock In/Out</h2>
        
        <?php if(isset($message)) echo "<p>$message</p>"; ?>
        
        <form method="post" action="">
            <?php if(!$clocked_in) { ?>
            <input type="submit" name="clock_in" value="Clock In">
            <?php } ?>
            <input type="submit" name="clock_out" value="Clock Out" style="background-color: red;">
        </form>

        <h3>Your Clock-In/Out Records</h3>
        <table>
            <tr>
                <th>Clock In Time</th>
                <th>Clock Out Time</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["clock_in_time"] . "</td><td>" . ($row["clock_out_time"] ? $row["clock_out_time"] : "Not clocked out yet") . "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No records found</td></tr>";
            }
            ?>
        </table>
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
</body>
</html>
