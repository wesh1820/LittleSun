<?php
if(isset($_GET['userid']) && !empty($_GET['userid'])) {
    $userId = $_GET['userid'];
    require_once './classes/db.class.php';
    $conn = $db->getConnection();
    $query = "SELECT COUNT(*) as count FROM user_tasks WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        echo 'true';
    } else {
        echo 'false';
    }
    $stmt->close();
    $conn->close();
} else {
    echo 'false';
}
?>
