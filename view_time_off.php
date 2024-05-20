<?php
require_once './classes/db.class.php';
require_once './classes/User.class.php';
require_once './classes/Session.class.php';
require './sidebar.php';

// Instantiate the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch user email from session
$email = Session::getSession('email');
$user = new User($conn);

    // Retrieve all time off requests from the database excluding those with status 1
    $stmt = $conn->prepare("SELECT timeoff.*, users.firstname, users.lastname 
                            FROM timeoff 
                            JOIN users ON timeoff.UserID = users.id 
                            JOIN user_location ON users.id = user_location.user_id
                            WHERE timeoff.Status != 1");
    $stmt->execute();
    $result = $stmt->get_result();
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
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h1>Time Off Requests</h1>
        <?php if ($result->num_rows > 0): ?>
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
                <?php while ($row = $result->fetch_assoc()): ?>
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
            <p>No time off requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
