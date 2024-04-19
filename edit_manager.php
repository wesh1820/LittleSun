<?php
session_start();
require_once 'config.php';


// Check if manager ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Fetch manager details
$id = $_GET['id'];
$sql_select_manager = "SELECT id, firstname, lastname, email FROM users WHERE id = ?";
$stmt_select_manager = $conn->prepare($sql_select_manager);
$stmt_select_manager->bind_param("i", $id);
$stmt_select_manager->execute();
$result_manager = $stmt_select_manager->get_result();

if ($result_manager->num_rows !== 1) {
    // Manager not found
    header("Location: index.php");
    exit();
}

$row = $result_manager->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Assuming you want to update the password too
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update manager details
    $sql_update_manager = "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?";
    $stmt_update_manager = $conn->prepare($sql_update_manager);
    $stmt_update_manager->bind_param("ssssi", $firstname, $lastname, $email, $hashed_password, $id);
    
    if ($stmt_update_manager->execute()) {
        // Manager details updated successfully
        header("Location: index.php");
        exit();
    } else {
        // Error handling
        echo "Error: " . $sql_update_manager . "<br>" . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">    
    <h2>Edit Manager</h2>
    <form method="post">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" required>
        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" required>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo $row['email']; ?>" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter new password">
        <input type="submit" value="Update">
    </form>
</div>
</body>
</html>

<?php
// Close connections
$stmt_select_manager->close();
$conn->close();
?>
