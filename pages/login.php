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
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/login.css">
	</header>


	<body>
		<form id="login" method="POST" action="../php/login.php">
			<h1>Login</h1>

			<label>Mail:</label> <input type="email" name="mail" required> <br>
			<label>Password:</label> <input type="password" name="password" required> <br>
		
			<input type="submit" value="Login" name="login" method="POST">
		</form>
	</body>
</html>