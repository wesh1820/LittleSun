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

$userTaskManager = new Task($conn);
$result_users_tasks = $userTaskManager->getUsersWithTasks();
$days_off = $userTaskManager->getUserDaysOff();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub users</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Hub users</h2>
        <?php if ($result_users_tasks->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Tasks</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result_users_tasks->fetch_assoc()) : ?>
                    <?php 
                    // Check if the user is on a day off
                    $today = date('Y-m-d');
                    $is_day_off = isset($days_off[$row['user_id']]) && in_array($today, $days_off[$row['user_id']]);
                    ?>
                    <?php if (!$is_day_off) : ?> <!-- Only display the user if they are not on a day off -->
                        <tr>
                            <td><?php echo $row['firstname']; ?></td>
                            <td><?php echo $row['lastname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['taskname']; ?></td>
                            <td>
                                <a href='edit_user.php?id=<?php echo $row['user_id']; ?>'>Edit</a> | 
                                <a href='delete_user.php?id=<?php echo $row['user_id']; ?>'>Delete</a> | 
                                <a href='#' class='add-button' data-user-id='<?php echo $row['user_id']; ?>'>Add Task</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No hub users found.</p>
        <?php endif; ?>
    </div>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="popup-content"></div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // When Add Task button is clicked
        $(".add-button").click(function(event) {
            event.preventDefault(); // Prevent the default action of the anchor tag
            var userId = $(this).data('user-id');
            var today = '<?php echo date('Y-m-d'); ?>';

            // Check if the user is on a day off
            if (typeof <?php echo json_encode($days_off); ?>[userId] !== 'undefined' && <?php echo json_encode($days_off); ?>[userId].includes(today)) {
                alert("Cannot add tasks for this user on their day off.");
            } else {
                // If not on day off, proceed to load the task addition form
                $("#popup-content").load("add_user_tasks.php");
                $("#myModal").css("display", "block");
            }
        });

        // Close the modal when clicking on the close button or outside the modal
        $(".close, .modal").click(function(event) {
            event.preventDefault(); // Prevent the default action of the anchor tag or propagation to the modal
            $("#myModal").css("display", "none");
        });

        // Prevent event propagation when clicking inside the modal content
        $(".modal-content").click(function(event) {
            event.stopPropagation();
        });
    });
    </script>
</body>
</html>
