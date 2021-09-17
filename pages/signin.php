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

		
		<form id="signin-form" method="POST" action="../php/signin.php">


			<div id="signin-content">
			<h1>Registrati</h1>
				<div class="content-box" id="signin-container">

					<label>Mail:</label> <input type="email" name="mail" required>
					<label>Password:</label> <input type="password" name="password" required> 
					<label>Conferma password:</label> <input type="password" name="password" required>

					<input type="submit" value="Iscriviti" name="signin" method="POST">

				</div>	
				<p>Sei già registrato? Torna al <a href="../pages/login.php">login</a></p>
			</div>
		
			
				

		</form>

		<?php require("../html/footer.php");?>

	</body>
</html>