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


		$phrases = [	"Un ottimo modo per studiare!", 
						"Per chi si fa il mazzo!", 
						"Per chi mette le carte cattedra!",
						"Il tuo compagno di studio!"];
		$p = $phrases[array_rand($phrases, 1)];
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
				<input type="email" id="mail_txt" class="login-text" name="mail" required>
				<label id="lbl_password">Password:</label> 
				<input type="password" id="password" name="password"  class="login-text" required>
				<label id="msg"></label>
				<div id="register" onclick="window.location.href='../pages/signin.php'">Registrati</div>
				<input type="submit" id="btn-submit" value="Login" name="login" method="POST">
			</form>

			<div id="login-side">
				<h1>Unicards</h1>
				<h2><?php global $p; echo $p;?></h2>
			</div>
			
		</div>

		<?php require("../html/footer.php");?>
	</body>
</html>