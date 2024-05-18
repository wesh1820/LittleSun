<?php
require_once './classes/time_off.class.php'; 

// Instantiate Timeoff class with database connection
$user = new Timeoff($conn);

// Handle accept/deny actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && isset($_POST["request_id"])) {
    $action = $_POST["action"];
    $request_id = $_POST["request_id"];
    echo "Processing request with ID: $request_id"; // Debugging
    if ($action === "accept") {
        $success = $user->acceptTimeOffRequest($request_id);
        $message = $success ? "Time off request accepted successfully!" : "Error accepting time off request.";
    } elseif ($action === "deny") {
        $success = $user->denyTimeOffRequest($request_id);
        $message = $success ? "Time off request denied successfully!" : "Error denying time off request.";
    }

    // Redirect to the same page after processing
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Retrieve all time off requests
$time_off_requests = $user->getTimeOffRequests();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Off Requests</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php if (!empty($time_off_requests)): ?>
        <div class="sidebar">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="container">
            <h1>Time Off Requests</h1>
            <table>
                <tr>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Date Off</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($time_off_requests as $request): ?>
                    <tr>
                        <td><?= $request['firstname'] ?></td>
                        <td><?= $request['lastname'] ?></td>
                        <td><?= $request['date_off'] ?></td>
                        <td><?= $request['status'] ?></td>
                        <td>
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <button type="submit" name="action" value="accept">Accept</button>
                                <button type="submit" name="action" value="deny">Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No time off requests found.</p>
    <?php endif; ?>
</body>
</html>
