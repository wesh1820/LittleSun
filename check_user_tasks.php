<?php
// Check if the user ID is provided
if(isset($_GET['userid']) && !empty($_GET['userid'])) {
    $userId = $_GET['userid'];

    // Query to check if the user has tasks assigned
    // You need to implement your own database connection and query logic here
    require_once './classes/db.class.php';

    // Assuming you have a database connection
    // Replace this with your actual database connection code
    $conn = $db->getConnection();

    // Query to check if the user has tasks assigned
    $query = "SELECT COUNT(*) as count FROM user_tasks WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if the user has tasks assigned
    if ($row['count'] > 0) {
        // User has tasks assigned
        echo 'true';
    } else {
        // User doesn't have any tasks assigned
        echo 'false';
    }

    // Close database connection
    $stmt->close();
    $conn->close();
} else {
    // User ID is not provided
    echo 'false';
}
?>
