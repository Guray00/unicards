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
		<link rel="stylesheet" href="../css/layout/login.css">

		<script type="text/javascript" src="../js/login.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</header>


	<body>

		
		<form name="signin" id="signin-form" method="POST" action="../php/signin.php">

	
			<div id="signin-content">

			<br>
			<h1>Registrati</h1>

			<div class="img-container" id="img">
				<img src="../assets/users/test@test.it.png"/>
			</div>
			
				<div class="content-box" id="signin-container">

					<label>Mail:</label> <input type="email" name="mail" required>
					<label>Username:</label> <input type="text" name="mail" required>
					<label>Password:</label> <input type="password" name="psw" required> 
					<label>Conferma password:</label> <input type="password" name="psw-confirm" required>

					<button type="submit">Iscriviti!</button>
				</div>	
				<br>
				<p>Sei già registrato? Torna al <a href="../pages/login.php">login</a></p>
			</div>

		</form>

		<?php require("../html/footer.php");?>

	</body>
</html>