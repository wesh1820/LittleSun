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
            <h2 form__title>Sign Up</h2>

            <?php if(isset($signupError)): ?>
                <div class="form__error">
                    <p>Sorry, there was an error signing you up. Please try again.</p>
                </div>
            <?php endif; ?>

            <div class="form__field">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" required>
            </div>
            <div class="form__field">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" required>
            </div>
            <div class="form__field">
                <label for="typeOfUser">Type of User</label>
                <select name="typeOfUser" required>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="form__field">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form__field">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" name="phoneNumber" required>
            </div>
            <div class="form__field">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form__field">
                <input type="submit" name="signup" value="Sign up" class="btn btn--primary">
            </div>
        </form>
    </div>
</div>
</body>
</html>
