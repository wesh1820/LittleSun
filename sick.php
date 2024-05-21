<?php
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';
require_once './classes/Task.class.php';

$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$conn = $db->getConnection();
$id = Session::getSession('id');
$user = new User($conn);
$user_role = $user->getUserRole($id);
$selected_day = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');

if (!strtotime($selected_day)) {
    $selected_day = date('Y-m-d');
}
$sickUsers = new User($conn);
$sick_users = $sickUsers->getSickUsersByDate($selected_day);
$task = new Task($conn);
$Tasks = $task->getAllTasks();
$task_map = array_column($Tasks, 'TaskName', 'TaskID');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sick Users</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="sidebar">
    <?php include 'sidebar.php'; ?>
</div>
<div class="container">
    <h2>Sick, <?php echo date('F j, Y', strtotime($selected_day)); ?></h2>
    <form method="GET" action="">
        <label for="day">Select Date:</label>
        <input type="date" id="day" name="day" value="<?php echo $selected_day; ?>">
        <button class="view-button" type="submit">View</button>
    </form>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="popup-content"></div>
        </div>
    </div>

    <?php if (!empty($sick_users)): ?>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Task</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sick_users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($task_map[$user['TaskID']]); ?></td>
                        <td><?php echo htmlspecialchars($user['StartSlot']); ?></td>
                        <td><?php echo htmlspecialchars($user['EndSlot']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users were sick on this day.</p>
    <?php endif; ?>
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
