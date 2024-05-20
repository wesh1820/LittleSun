<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>User Tasks and Time Slots</title>
</head>
<body>
<div class="container">
    <?php
require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';
require_once './classes/clock.class.php';
require './sidebar.php';

// Instantiate the database
$db = Database::getInstance();
$conn = $db->getConnection();
    include 'sidebar.php';

    class UserTask {
        private $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        public function getUserTasksAndTimeSlots($user_id) {
            $sql = "SELECT 
                        time_slots.TimeSlotID, 
                        tasks.TaskName, 
                        time_slots.StartSlot, 
                        time_slots.EndSlot, 
                        time_slots.Date, 
                        time_slots.Sick
                    FROM 
                        UserTasks
                    INNER JOIN 
                        tasks ON UserTasks.TaskID = tasks.TaskID
                    INNER JOIN 
                        time_slots ON UserTasks.TaskID = time_slots.TaskID
                    WHERE 
                        UserTasks.UserID = ? 
                        AND time_slots.UserID = ? 
                        AND time_slots.Sick = 0";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h3>Your Tasks and Time Slots:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Task Name</th><th>Start Time</th><th>End Time</th><th>Date</th><th>Sick</th><th>Action</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["TaskName"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["StartSlot"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["EndSlot"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Date"]) . "</td>";
                    echo "<td>" . ($row["Sick"] ? "Yes" : "No") . "</td>";
                    echo "<td>
                            <form method='POST' action=''>
                                <input type='hidden' name='timeslot_id' value='" . $row['TimeSlotID'] . "'>
                                <button class='view-button' type='submit' name='mark_sick'>Mark as Sick</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No tasks and time slots found for this user.";
            }
        }

        public function setSick($timeslot_id) {
            $sql = "UPDATE time_slots SET Sick = 1 WHERE TimeSlotID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $timeslot_id);
            if ($stmt->execute()) {
                echo "The selected time slot has been marked as sick.";
            } else {
                echo "Error: " . $this->conn->error;
            }
        }

        public function setSickForDay($user_id, $date) {
            $sql = "UPDATE time_slots SET Sick = 1 WHERE UserID = ? AND Date = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $user_id, $date);
            if ($stmt->execute()) {
                echo "All time slots for $date have been marked as sick.";
            } else {
                echo "Error: " . $this->conn->error;
            }
        }
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userTasks = new UserTask($conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['mark_sick'])) {
                $timeslot_id = intval($_POST['timeslot_id']);
                $userTasks->setSick($timeslot_id);
            }

            if (isset($_POST['mark_sick_day'])) {
                $date = $_POST['date'];
                $userTasks->setSickForDay($user_id, $date);
            }
        }

        // Form to mark all time slots for a specific day as sick
        echo "<h2>Mark All Time Slots for a Day as Sick:</h2>";
        echo "<form method='POST' action=''>
                <label for='date'>Date:</label>
                <input type='date' name='date' required>
                <button class='view-button' type='submit' name='mark_sick_day'>Mark as Sick for the Whole Day</button>
              </form>";

        $userTasks->getUserTasksAndTimeSlots($user_id);
    } else {
        echo "You are not logged in!";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
