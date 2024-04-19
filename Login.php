<?php
session_start();
require_once 'config.php';

function canLogin($email, $password) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($email);
    $query = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if (!$hashedPassword || !password_verify($password, $hashedPassword)) {
        return false; 
    }

    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $id; // Set the user_id session variable

    return true;
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (canLogin($email, $password)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Your Site</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if(isset($error)): ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Email:</label>
        <input type="text" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
</div>
</body>
</html>
