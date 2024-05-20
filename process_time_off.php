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

// Validate input and process the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Print received POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Check if all required fields are filled
    if (isset($_POST["user_id"], $_POST["reason"], $_POST["time_off_type"])) {
        // Retrieve and sanitize user ID from form data
        $user_id = intval($_POST["user_id"]); // Convert to integer

        if ($user_id <= 0) {
            echo "Invalid user ID: " . $user_id;
            exit; // Exit script
        }

        // Additional checks based on time off type
        if ($_POST["time_off_type"] === "single_day" && isset($_POST["single_day_date"], $_POST["single_day_start_time"], $_POST["single_day_end_time"])) {
            // Single day off form fields
            echo "Single day selected"; // Debugging statement
            $reason = $_POST["reason"];
            $start_date = $_POST["single_day_date"];
            $start_time = $_POST["single_day_start_time"];
            $end_time = $_POST["single_day_end_time"];
            $status = 0; // 'Pending' status

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
                    echo "Single day time off request submitted successfully.";
                } else {
                    echo "Error executing query: " . $stmt->error;
                }
                $stmt->close();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } elseif ($_POST["time_off_type"] === "multiple_days" && isset($_POST["start_date"], $_POST["end_date"])) {
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
