<?php
require_once 'config.php';

function changePassword($email, $oldPassword, $newPassword) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($email);
    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (!$hashedPassword || !password_verify($oldPassword, $hashedPassword)) {
        return false; 
    }

    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $hashedNewPassword, $email);
    $result = $updateStmt->execute();

    $updateStmt->close();
    $stmt->close();
    $conn->close();

    return $result;
}

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {

    header("Location: login.php");
    exit();
}

$error = false;

if (!empty($_POST['changepassword'])) {
    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $oldPassword = $_POST['oldPassword'];
        $newPassword = $_POST['newPassword'];

        if (changePassword($email, $oldPassword, $newPassword)) {

            header("Location: index.php"); 
            exit();
        } else {
            $error = true; 
        }
    } else {

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Littlesun</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="changepassword">
    <div class="form form--changepassword">
        <form action="" method="post">
            <h2 form__title>Change Password</h2>

            <?php if($error): ?>
                <div class="form__error">
                    <p>Sorry, there was an error changing your password. Please try again.</p>
                </div>
            <?php endif; ?>

            <div class="form__field">
                <label for="oldPassword">Old Password</label>
                <input type="password" name="oldPassword" required>
            </div>
            <div class="form__field">
                <label for="newPassword">New Password</label>
                <input type="password" name="newPassword" required>
            </div>

            <div class="form__field">
                <input type="submit" name="changepassword" value="Change Password" class="btn btn--primary">
            </div>
        </form>
    </div>
</div>
</body>
</html>
