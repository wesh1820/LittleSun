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
    <div class="container">
        <h1>Ask Time Off</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
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
            <div id="time_off_type">
                <input type="checkbox" id="multiple_days" name="time_off_type[]" value="multiple_days">
                <label for="multiple_days">Multiple Days</label>
                <input type="checkbox" id="single_day" name="time_off_type[]" value="single_day">
                <label for="single_day">Single Day</label>
            </div>
            <br><br>
            <div id="single_day_details" style="display:none;">
                <label for="single_day_date">Date Off:</label>
                <input type="date" id="single_day_date" name="single_day_date">
                <br><br>
                <label for="single_day_start_time">Start Time Slot:</label>
                <select id="single_day_start_time" name="single_day_start_time">
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
                <label for="single_day_end_time">End Time Slot:</label>
                <select id="single_day_end_time" name="single_day_end_time">
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
            <div id="multiple_days_details" style="display:none;">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date">
                <br><br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            <br><br>
            <input type="submit" value="Submit">
        </form>
    </div>

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

$email = Session::getSession('email');

// Instantiate DB class

$conn = $db->getConnection();

$user = new User($conn);
$user_role = $user->getUserRole($email);
Session::setSession('firstname', $user->getFirstName($email));

    // Validate input and process the form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        // Check if all required fields are filled
        if (isset($_POST["user_id"], $_POST["reason"], $_POST["time_off_type"])) {
            // Retrieve and sanitize user ID from form data
            $user_id = intval($_POST["user_id"]); // Convert to integer

            if ($user_id <= 0) {
                echo "Invalid user ID: " . $user_id;
                exit; // Exit script
            }

            // Additional checks based on time off type
            if (in_array("single_day", $_POST["time_off_type"]) && isset($_POST["single_day_date"], $_POST["single_day_start_time"], $_POST["single_day_end_time"])) {
                // Single day off form fields
                echo "Single day selected"; // Debugging statement
                $reason = $_POST["reason"];
                $start_date = $_POST["single_day_date"];
                $start_time = $_POST["single_day_start_time"];
                $end_time = $_POST["single_day_end_time"];
                $status = 0; // 'Pending
                // Debugging: Print extracted form data
                echo "UserID: $user_id<br>";
                echo "Reason: $reason<br>";
                echo "Start Date: $start_date<br>";
                echo "Start Time: $start_time<br>";
                echo "End Time: $end_time<br>";

                // Insert single day off data into the database
                try {
                    $sql = "INSERT INTO timeoff (UserID, Timeoff_reason, Start_date, Start_time, End_time, Status) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issssi", $user_id, $reason, $start_date, $start_time, $end_time, $status);
                    if ($stmt->execute()) {
                    } else {
                        echo "Error executing query: " . $stmt->error;
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            } elseif (in_array("multiple_days", $_POST["time_off_type"]) && isset($_POST["start_date"], $_POST["end_date"])) {
                // Multiple days off form fields
                echo "Multiple days selected"; // Debugging statement
                $reason = $_POST["reason"];
                $start_date = $_POST["start_date"];
                $end_date = $_POST["end_date"];
                $status = 0; // 'Pending' status

                // Proceed with database operation...
            } else {
                echo "Not all required fields are filled for the selected time off type.";
            }
        } else {
            echo "Not all required fields are filled.";
        }
    } else {
        echo "The form was not submitted.";
    }
    ?>
    <script>
        function toggleTimeSlots() {
            var singleDayDetails = document.getElementById("single_day_details");
            var multipleDaysDetails = document.getElementById("multiple_days_details");
            var singleDayCheckbox = document.getElementById("single_day");
            var multipleDaysCheckbox = document.getElementById("multiple_days");

            if (singleDayCheckbox.checked) {
                singleDayDetails.style.display = "block";
                multipleDaysDetails.style.display = "none";
            } else if (multipleDaysCheckbox.checked) {
                singleDayDetails.style.display = "none";
                multipleDaysDetails.style.display = "block";
            } else {
                singleDayDetails.style.display = "none";
                multipleDaysDetails.style.display = "none";
            }
        }

        function validateForm() {
            var singleDayCheckbox = document.getElementById("single_day");
            var multipleDaysCheckbox = document.getElementById("multiple_days");
            if (!singleDayCheckbox.checked && !multipleDaysCheckbox.checked) {
                alert("Please select a time off type.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        document.getElementById("single_day").addEventListener("change", toggleTimeSlots);
        document.getElementById("multiple_days").addEventListener("change", toggleTimeSlots);
    </script>
</body>
</html>
