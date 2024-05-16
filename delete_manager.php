<?php
require_once 'config.php';

// Check if ID parameter is set in the URL
if (isset($_GET['id'])) {
    $manager_id = $_GET['id'];

    // Delete manager from the database
    $sql_delete_manager = "DELETE FROM users WHERE id = ?";
    $stmt_delete_manager = $conn->prepare($sql_delete_manager);
    $stmt_delete_manager->bind_param("i", $manager_id);

    if ($stmt_delete_manager->execute()) {
        // Redirect back to index.php after successful deletion
        header("Location: manager.php");
        exit();
    } else {
        // Error handling
        echo "Error deleting manager: " . $conn->error;
    }

    $stmt_delete_manager->close();
} else {
    // Redirect back to index.php if ID parameter is not set
    header("Location: manager.php");
    exit();
}

$conn->close();
?>
