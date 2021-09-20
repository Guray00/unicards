<!DOCTYPE html>

<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		
		// se il login è già avvenuto reindirizza al menu
		if (_isLogged()){
			header("location: ../pages/dashboard.php");
			exit();
		}

		$phrases = [	"Un ottimo modo per studiare!", 
						"Per chi si fa il mazzo!", 
						"Per chi mette le carte in cattedra!",
						"Il tuo compagno di studio!", 
						"Affrontare lo studio con le carte in regola!",
						"Giocare per studio, studiare per gioco!"];

		$p = $phrases[array_rand($phrases, 1)];
?>

<html>
	<header>
		<title>Unicards Login</title>
		<link rel="icon" type="image/png" href="../assets/favicon.png"/>
		<meta charset="utf-8">

		<link rel="stylesheet" href="../css/layout/login.css">
		<link rel="stylesheet" href="../css/theme.css">
		
		<script type="text/javascript" src="../js/alertbox.js"></script>
		<script type="text/javascript" src="../js/login.js"></script>
	</header>


	<body>

	<?php 
		// se viene passato un codice restituisce un messaggio
		if (isset($_GET) && isset($_GET["code"])){

			// login errato
			if ($_GET["code"] == -1){
				echo '	<script type="text/javascript">',
						'okbox({
							title: "Credenziali Errate", 
							content:"Mi dispiace, non è possibile fare l\'accesso. Controlla che le credenziali inserite siano giuste.",
				
							ok: function(){}
						});',
						'</script>'
					;
			} 
		}
	?>

		<div id="login_container">
			
			<form id="login" method="POST" action="../php/login.php">
				<h1>Login</h1>

				<label id="lbl_mail">Mail:</label> 
				<input type="email" id="mail_txt" class="login-text" name="mail" placeholder="user@mail.com" required>
				<label id="lbl_password">Password:</label> 
				<input type="password" id="password" name="password"  class="login-text" placeholder="********" required>
				<label id="msg"></label>
				<div id="register" onclick="window.location.href='../pages/signin.php'">Registrati</div>
				<input type="submit" id="btn-submit" value="Login" name="login" method="POST">
			</form>

			<div id="login-side">
				<h1><a href="../pages/landing.html">Unicards</a></h1>
				<h2><?php global $p; echo $p;?></h2>
			</div>
			
		</div>

		<?php require("../html/footer.php");?>
	</body>
</html>