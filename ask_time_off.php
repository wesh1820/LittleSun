<?php
// Main file

require_once './classes/Location.class.php';
require_once './classes/Task.class.php'; 
require_once './classes/db.class.php';
require_once './classes/Session.class.php';
require_once './classes/User.class.php';

$conn = $db->getConnection();
$email = Session::getSession('email');
$user = new User($conn);
$user_role = $user->getUserRole($email);

if ($user_role !== 'user') {
    echo "Access denied. Only manager can view this page.";
    exit;
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["user_id"], $_POST["reason"], $_POST["time_off_type"])) {
            $user_id = intval($_POST["user_id"]); 
            if ($user_id <= 0) {
                echo "Invalid user ID: " . $user_id;
                exit; 
            }

            if (in_array("single_day", $_POST["time_off_type"]) && isset($_POST["single_day_date"], $_POST["single_day_start_time"], $_POST["single_day_end_time"])) {
                echo "Single day selected"; 
                $reason = $_POST["reason"];
                $start_date = $_POST["single_day_date"];
                $start_time = $_POST["single_day_start_time"];
                $end_time = $_POST["single_day_end_time"];
                $status = 0; 
                echo "UserID: $user_id<br>";
                echo "Reason: $reason<br>";
                echo "Start Date: $start_date<br>";
                echo "Start Time: $start_time<br>";
                echo "End Time: $end_time<br>";
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
                echo "Multiple days selected"; 
                $reason = $_POST["reason"];
                $start_date = $_POST["start_date"];
                $end_date = $_POST["end_date"];
                $status = 0; 
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
