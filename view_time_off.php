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
    <?php 
    // Include the Timeoff class and establish database connection
    require_once './classes/time_off.class.php'; 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "littlesun";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user = new Timeoff($conn);

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['action'])) {
            $request_id = $_POST['request_id'];
            if ($_POST['action'] == 'accept') {
                $user->updateStatus($request_id, 1);
            } elseif ($_POST['action'] == 'deny') {
                $user->updateStatus($request_id, 0);
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Retrieve all time off requests excluding those with status 1
    $time_off_requests = $user->getTimeOffRequests();
    ?>

    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h1>Time Off Requests</h1>
        <?php if (!empty($time_off_requests)): ?>
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
                <?php foreach ($time_off_requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['firstname'] . ' ' . htmlspecialchars($request['lastname'])) ?></td>
                        <td><?= htmlspecialchars($request['Timeoff_reason']) ?></td>
                        <td><?= htmlspecialchars($request['Start_date']) ?></td>
                        <td><?= htmlspecialchars($request['End_date']) ?></td>
                        <td><?= htmlspecialchars($request['Start_time']) ?></td>
                        <td><?= htmlspecialchars($request['End_time']) ?></td>
                        <td><?= htmlspecialchars($request['Status']) ?></td>
                        <td>
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                                <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['ID']) ?>">
                                <button type="submit" name="action" value="accept">Accept</button>
                                <button type="submit" name="action" value="deny">Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No time off requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
