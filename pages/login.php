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
		<link rel="stylesheet" href="../css/login.css">
		<link rel="stylesheet" href="../css/theme.css">
	</header>


	<body>

		<div id="login_container">
			<form id="login" method="POST" action="../php/login.php">
				<h1>Login</h1>

				<label id="lbl_mail">Mail:</label> 
				<input type="email" id="" name="mail" required>
				<label id="lbl_password">Password:</label> 
				<input type="password" id="password" name="password" required>
				<label id="msg">aaa</label>
				<input type="submit" id="btn" value="Login" name="login" method="POST">
			</form>
			
		</div>

		<!-- <div class="wave2"></div> -->
		<?php require("../html/footer.php");?>
	</body>
</html>