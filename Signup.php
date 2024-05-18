<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup - Littlesun</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="signup">
    <div class="form form--signup">
        <form action="" method="post">
        <div class="container">
        <div class="logo">
            <img src="../LittleSun-main/css/images/Logo.svg" alt="Logo">
        </div>
        <h2>Welcome back! Let's get started.</h2>

            <?php if(isset($signupError)): ?>
                <div class="form__error">
                    <p>Sorry, there was an error signing you up. Please try again.</p>
                </div>
            <?php endif; ?>

            <div class="form__field">
                <label for="firstname" >Firstname</label>
                <input type="text" name="firstname" id="firstname" placeholder="Alexander" required>
            </div>
            <div class="form__field">
                <label for="lastname">Lastname</label>
                <input type="text" name="lastname" id="lastname" placeholder="Martinez" required>
            </div>
            <div class="form__field">
                <label for="typeOfUser">Type of User</label>
                <select name="typeOfUser" id="typeOfUser" required>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="form__field">
                <label for="email">E-mail address</label>
                <input type="email" name="email" id="email" placeholder="alexander.martinez@example.com" required>
            </div>
            <div class="form__field">
                <label for="phoneNumber" >Phone Number</label>
                <input type="text" name="phoneNumber" id="phoneNumber" placeholder="+32 487 25 84 35" required>
            </div>
            <div class="form__field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="*************" required>
            </div>

            <div class="form__field">
                <input type="submit" name="signup" value="Sign up" class="btn btn--primary">
            </div>
        </form>
    </div>
</div>
</body>
</html>
