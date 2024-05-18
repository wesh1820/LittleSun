<?php
require_once 'config.php'; // Include the config file which starts the session

// Assuming user ID is stored in session

$user_id = $_SESSION['user_id']; // Ensure user ID is stored in session

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
            <label for="date_off">Date Off:</label>
            <input type="date" id="date_off" name="date_off" required>
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
                    $start_time = strtotime('08:00');
                    while ($start_time <= $end_time) {
                        echo '<option value="' . date('H:i', $start_time) . '">' . date('h:i A', $start_time) . '</option>';
                        $start_time += (30 * 60);
                    }
                ?>
            </select>
            <br><br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
