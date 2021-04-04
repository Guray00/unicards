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
	<body>
		<h1>Login</h1>

		<form id="login" method="POST" action="../php/login.php">
					
			<label>Mail:</label> <input type "text" name="mail" required> <br>
			<label>Password:</label> <input type "text" name="password" required> <br>
		
			<input type="submit" value="Login" name="login" method="POST">
		</form>
	</body>
</html>