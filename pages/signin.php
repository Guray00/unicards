<!DOCTYPE html>

<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		
		if (_isLogged()){
			echo "Utente già connesso <br>";
			echo '<a href="../php/logout.php">disconnetti</a>';
			exit();
		}
?>

<html>
	<header>
		<title>unicards</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/login.css">
	</header>


	<body>
		<form id="signin" method="POST" action="../php/signin.php">
			<h1>Sign In</h1>

			<label>Mail:</label> <input type="email" name="mail" required> <br>
			<label>Password:</label> <input type="password" name="password" required> <br>
		
			<input type="submit" value="Sign In" name="signin" method="POST">
		</form>
	</body>
</html>