<!DOCTYPE html>

<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		
		if (_isLogged()){
			echo "Utente già connesso <br>";
			echo '<a href="../php/logout.php">disconnetti</a>';
			header("location: ./dashboard.php");
			exit();
		}
?>

<html>
	<header>
		<title>unicards</title>
		<link rel="stylesheet" href="../css/layout/login.css">
		<link rel="stylesheet" href="../css/theme.css">
		
		<script type="text/javascript" src="../js/login.js"></script>
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

			<div id="login-side">
				<h1>Unicards</h1>
				<h2>Un ottimo modo per studiare</h2>
			</div>
			
		</div>

		<!-- <div class="wave2"></div> -->
		<?php require("../html/footer.php");?>
	</body>
</html>