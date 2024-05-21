<?php

require_once './config.php';

function canLogin($email, $password) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($email);
    $query = "SELECT id, password, typeOfUser FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->bind_result($id, $hashedPassword, $role);
    $stmt->fetch();

    if (!$hashedPassword || !password_verify($password, $hashedPassword)) {
        return false; 
    }

    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $id; // Set the user_id session variable
    $_SESSION['role'] = $role; // Set the role session variable

    return true;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (canLogin($email, $password)) {
        if ($_SESSION['role'] == 'admin') {
            header("Location: index.php");
        } elseif ($_SESSION['role'] == 'manager') {
            header("Location: user.php");
        } elseif ($_SESSION['role'] == 'user') {
            header("Location: clockin.php");
        }
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
<<<<<<< HEAD

    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-image: url("./uploads/littlesunorg_cover.jpeg");
    background-repeat: no-repeat;
    background-size: cover;
}

.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
}

.logo img {
    max-width: 250px;
}

.title {
    margin-bottom: 20px;
    font-size: 28px;
}

form {
    width: 600px;
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 0px 15px 0px rgba(0,0,0,0.1);
}

form label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
    font-size: 16px;
}

form input[type="text"],
form input[type="password"],
form input[type="email"] {
    width: calc(100% - 10px);
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

form input[type="checkbox"] {
    margin-right: 10px;
}

form input[type="submit"] {
    width: 100%;
    padding: 15px;
    background-color: black;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    margin-top: 20px;
}

form input[type="submit"]:hover {
    background-color:  #e9ca01;
}

.error {
    color: red;
    margin-bottom: -48px;
    font-size: 18px;
    z-index: 2;
}

.forgot-password {
    margin-top: 10px;
    font-size: 16px;
}

.toggle-password {
    cursor: pointer;
    position: relative;
    font-size: 16px;
}

@media (max-width: 768px) {
    form {
        width: 90%;
    }
}



    </style>
</head>
<body>
    <div class="container">
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="email">E-mail address</label>
            <input type="email" id="email" name="email" placeholder="Alexander.martinez@example.com" required><br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="*************" required>
            <input type="checkbox" id="remember-me" name="remember-me">
            <label for="remember-me">Remember me</label>
            <a href="forgot_password.php">Forgot your password?</a><br>
            <input type="submit" value="Login">
        </form>
    </div>
    <script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            document.querySelector('.toggle-password').textContent = 'Hide';
        } else {
            passwordInput.type = "password";
            document.querySelector('.toggle-password').textContent = 'Show';
        }
    }
    </script>
=======
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>

    * {
        background-color: #F8F8F8;
    }
        
    .container {
        margin: 0;
    }

    .logo {
        display: flex;
        justify-content: center; /* Centreert het logo horizontaal */
        margin-bottom: 20px; /* Optioneel: ruimte onder het logo */
    }

    .logo img {
    display: block;
    }

    .title, form {
        margin-left: 10px; /* Optioneel: behoud de linker marge voor de rest van de inhoud */
    }

    </style>

</head>
<body>
<div class="container">
<div class="logo">
    <img src="../LittleSun/css/images/Logo.svg" alt="Logo">
</div>

    <h2 class="title">Welcome back! Let's get started.</h2>
    <?php if(isset($error)): ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>E-mail address</label>
        <input type="text" name="email" placeholder="Alexander.martinez@example.com" required><br>
        <label>Password</label>
        <input type="password" name="password" placeholder="*************" required><br>
        <input type="submit" value="Login">
    </form>
</div>
>>>>>>> 9fd0143fa0023f17c58364575694595a0274282d
</body>
</html>
