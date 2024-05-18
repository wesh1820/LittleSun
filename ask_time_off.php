<?php
// Start the session (if not already started)
session_start();

// Check if the user is logged in and if their user ID is stored in the session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Retrieve the user ID from the session
} else {
    // Handle the case where the user is not logged in or the user ID is not set in the session
    // For example, redirect the user to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask Time Off</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="container">
        <h1>Ask Time Off</h1>
        <form action="process_time_off.php" method="POST">
            <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

            <label for="reason">Reason:</label>
            <select id="reason" name="reason" required>
                <option value="" disabled selected>Select a reason</option>
                <option value="Sick">Sick</option>
                <option value="Personal">Personal</option>
                <option value="Vacation">Vacation</option>
            </select>
            <br><br>
            <label for="time_off_type">Select Time Off Type:</label>
            <select id="time_off_type" name="time_off_type" required onchange="toggleTimeSlots()">
                <option value="single_day">Single Day</option>
                <option value="multiple_days">Multiple Days</option>
            </select>
            <br><br>
            <div id="single_day" style="display:block;">
                <label for="start_date">Date Off:</label>
                <input type="date" id="start_date" name="start_date" required>
                <br><br>
                <label for="start_time_slot">Start Time Slot:</label>
                <select id="start_time_slot" name="start_time_slot" required>
                    <option value="" disabled selected>Select start time slot</option>
                    <?php
                        $start_time = strtotime('08:00');
                        $end_time = strtotime('20:00');
                        while ($start_time <= $end_time) {
                            echo '<option value="' . date('H:i', $start_time) . '">' . date('h:i A', $start_time) . '</option>';
                            $start_time += (30 * 60);
                        }
                    ?>
                </select>
                <br><br>
                <label for="end_time_slot">End Time Slot:</label>
                <select id="end_time_slot" name="end_time_slot" required>
                    <option value="" disabled selected>Select end time slot</option>
                    <?php
                        // Display the same time slots as start time for single day off
                        $start_time = strtotime('08:00');
                        while ($start_time <= $end_time) {
                            echo '<option value="' . date('H:i', $start_time) . '">' . date('h:i A', $start_time) . '</option>';
                            $start_time += (30 * 60);
                        }
                    ?>
                </select>
            </div>
            <div id="multiple_days" style="display:none;">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
                <br><br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>
            <br><br>
            <input type="submit" value="Submit">
        </form>
    </div>

    <script>
function toggleTimeSlots() {
    var timeOffType = document.getElementById("time_off_type").value;
    var singleDayDiv = document.getElementById("single_day");
    var multipleDaysDiv = document.getElementById("multiple_days");

    if (timeOffType === "single_day") {
        singleDayDiv.style.display = "block";
        multipleDaysDiv.style.display = "none";
    } else if (timeOffType === "multiple_days") {
        singleDayDiv.style.display = "none";
        multipleDaysDiv.style.display = "block";
    } else {
        singleDayDiv.style.display = "none"; // Hide single day div
        multipleDaysDiv.style.display = "none"; // Hide multiple days div
    }
}

    </script>
</body>
</html>
