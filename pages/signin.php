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
		<link rel="icon" type="image/png" href="../assets/favicon.png"/>


		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/layout/login.css">

		<script type="text/javascript" src="../js/alertbox.js"></script>
		<script type="text/javascript" src="../js/login.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</header>


	<body>

		<?php 
			if (isset($_GET) && isset($_GET["code"])){
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
				<h1>Registrati</h1>

				<div class="img-container" id="img">
					<img src="../assets/users/test@test.it.png"/>
				</div>
				
					<div class="content-box" id="signin-container">

						<label>Mail:*</label> <input type="email" name="mail" placeholder="user@mail.com" required>
						<label>Username:*</label> <input type="text" name="username" required>
						<label>Password:*</label> <input type="password" name="password" id="psw" required> 
						<label>Conferma password:*</label> <input type="password" name="psw-confirm" id ="psw-confirm" required>

						<button type="submit" name="signin">Iscriviti!</button>
					</div>	
					<br>
					<p>Sei già registrato? Torna al <a href="../pages/login.php">login</a></p>
			</div>
		</form>

		<?php require("../html/footer.php");?>

	</body>
</html>