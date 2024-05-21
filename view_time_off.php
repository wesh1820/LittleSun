<?php
require_once './classes/db.class.php';
require_once './classes/Task.class.php';

$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$taskManager = new Task($db);
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && in_array($_POST['action'], ['accept', 'deny'])) {
        $request_id = $_POST['request_id'];
        $new_status = $_POST['action'] == 'accept' ? 1 : 2;
        echo $taskManager->updateStatus($request_id, $new_status);
    }
}

$result = $taskManager->getTimeOffRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Off Requests</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h1>Time Off Requests</h1>
        <?php if ($db->numRows($result) > 0): ?>
            <table>
                <tr>
                    <th>User</th>
                    <th>Timeoff Reason</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $db->fetchAssoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                        <td><?= htmlspecialchars($row['Timeoff_reason']) ?></td>
                        <td><?= htmlspecialchars($row['Start_date']) ?></td>
                        <td><?= htmlspecialchars($row['End_date']) ?></td>
                        <td><?= htmlspecialchars($row['Start_time']) ?></td>
                        <td><?= htmlspecialchars($row['End_time']) ?></td>
                        <td><?= htmlspecialchars($row['Status']) ?></td>
                        <td>
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                                <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['ID']) ?>">
                                <button class="view-button" type="submit" name="action" value="accept">Accept</button>
                                <button class="view-button" type="submit" name="action" value="deny">Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No Time off requests found.</p>
        <?php endif; ?>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/script.js"></script>
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

<?php
$db->closeConnection();
?>
