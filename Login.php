<?php
	function canLogin($pEmail, $pPassword){
		$conn = new mysqli("localhost", "root", "", "Littlesun"); 
		// in de videos wordt het op een andere manier gedaan
		$email = $conn->real_escape_string($pEmail);	
		$query = "SELECT password from users where email = '$email'";
		$result = $conn->query($query);
		$user = $result->fetch_assoc();
		if( password_verify($pPassword, $user['password'])) {
			return true;
		}
		else {
			return false;
		}

		
		var_dump($result);
		exit();
		
	}
	if( !empty($_POST)){
		$email = $_POST['email'];
		$password = $_POST['password'];
		if( canLogin($email, $password)) {

			session_start();
			$_SESSION['loggedin'] = true; 	// session is een array op de server
			header("Location: index.php");

		}
		else {
			$error = true;

		}
	}

?><!DOCTYPE html>
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
				<h2 form__title>Sign In</h2>

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
					<input type="submit" value="Sign in" class="btn btn--primary">	
					<input type="checkbox" id="rememberMe"><label for="rememberMe" class="label__inline">Remember me</label>
				</div>
			</form>
		</div>
	</div>
</body>
</html>