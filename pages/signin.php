<!DOCTYPE html>

<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");

		// se sono già loggato rimando alla dashboard
		if (_isLogged()){
			header("location: ../pages/dashboard.php");
			exit();
		}
?>

<html lang="it">
	<head>
		<title>unicards</title>
		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="../assets/favicon.png"/>


		<!-- stili -->
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/layout/login.css">

		<!-- Script -->
		<script src="../js/alertbox.js"></script>
		<script src="../js/signin.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>


	<body>
		<?php 

			// se viene passato un codice restituisce un messaggio
			if (isset($_GET) && isset($_GET["code"])){

				// registrazione non avvenuta perchè esiste un utente
				if ($_GET["code"] == 0){
					echo '	<script type="text/javascript">',

							'okbox({
								title: "Mail già utilizzata", 
								content:"Mi dispiace, la mail che hai usato è già stata utilizzata.",
					
								ok: function(){}
							});',

							'</script>'
						;
				}

				// registrazione non avvenuto per un motivo generico
				elseif ($_GET["code"] == -1){
					echo '	<script type="text/javascript">',

							'okbox({
								title: "Errore nella registrazione", 
								content:"Mi dispiace, qualcosa è andato storto nella registrazione.",
					
								ok: function(){}
							});',

							'</script>'
						;
				}

				// registrazione non avvenuta per errore nel form
				elseif ($_GET["code"] == -2){
					echo '	<script type="text/javascript">',

								'okbox({
									title: "Errore nella registrazione", 
									content:"Controlla di aver scritto correttamente tutti i valori nei campi.",
									ok: function(){}
								});',

							'</script>'
						;
				}

				// registrazione avvenuta con successo
				elseif ($_GET["code"] == 1){
					echo '	<script type="text/javascript">',

							'okbox({
								title: "Registrazione avvenuta con successo!", 
								content:"Complimenti, la registrazione è andata a buon fine. Verrai reindirizzato alla schermata di login.",
					
								ok: function(){window.location.href="./login.php";}
							});',

							'</script>'
						;
				}
			}
		?>

		<form id="signin-form" action="../php/signin.php" method="POST">
			<div id="signin-content">

				<br>
				<!-- titolo -->
				<h1>Registrati</h1>


				<!-- spazio per l'immagine -->
				<div id="img-port">
					<div class="img-container" id="img">
						<img src="../assets/icons/image.svg" alt="Avatar"/>
						<input name="upload" value="" id="fake-url">
					</div>

					<!-- necessario per salvare nel form il binary dell'immagine -->
					<input type="file" id="upload" accept="image/*">
				</div>
				

				<div class="content-box" id="signin-container">

					<label>Mail:*</label> <input type="email" name="mail" placeholder="user@mail.com" required>
					<label>Username:*</label> <input type="text" name="username" placeholder="username" required>
					<label>Password:*</label> <input type="password" name="password" id="psw" placeholder="********" required> 
					<label>Conferma password:*</label> <input type="password" name="psw-confirm" id ="psw-confirm" placeholder="********" required>

					<br>
					<button type="submit" name="signin" id="btn-signin">Iscriviti!</button>
				</div>	

				<br>
				<p>Sei già registrato? Torna al <a href="../pages/login.php">login</a></p>
			</div>
		</form>

		<!-- footer -->
		<?php require("../html/footer.php");?>

	</body>
</html>