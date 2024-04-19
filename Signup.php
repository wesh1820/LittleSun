<?php
//b-crypt, constante, salt met wachtwoord, hash: $2y$10$Zchgxzsdiezhvf.fd0/toZy/fveuzyezogfczp
    if(!empty($_POST)){
        //er is een submit
        //echo "ja";
        $email = $_POST['email'];
        $options = [
            'cost' => 16
        ];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);

        $conn = new mysqli("localhost", "root", "root", "netflix");
        $query = "insert into users (email, password) values ('" . $email . "', '" . $password . "')";
        session_start();
        $_SESSION['loggedin'] = true;
        $result = $conn->query($query);
        header("Location: index.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IMDFlix</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div class="netflixLogin">
		<div class="form form--login">
			<form action="" method="post">
				<h2 form__title>Sign Up</h2>

				<?php if(isset($error)): ?>
				<div class="form__error">
					<p>
						Sorry, we can't log you in with that email address and password. Can you try again?
					</p>
				</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="Email">Email</label>
					<input type="text" name="email">
				</div>
				<div class="form__field">
					<label for="Password">Password</label>
					<input type="password" name="password">
				</div>

				<div class="form__field">
					<input type="submit" value="Sign up" class="btn btn--primary">	
					<input type="checkbox" id="rememberMe"><label for="rememberMe" class="label__inline">Remember me</label>
				</div>
			</form>
            <a href="changePassword.php" target="_blank">This is a link</a>
		</div>
	</div>
</body>
</html>