<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$error = false;

if (!empty($_POST['changepassword'])) {
    $email = $_POST['email'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($oldPassword, $hashedPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ss", $hashedNewPassword, $email);
            $updateResult = $updateStmt->execute();

            if ($updateResult) {
                $updateStmt->close();
                $stmt->close();
                $conn->close();
                header("Location: index.php");
                exit();
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    } else {
        $error = true;
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

            <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>">


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
