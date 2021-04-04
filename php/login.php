
<?php
	require("./utils.php");
	require_once("./database.php");
	
	// se l'utente è già loggato è inutile eseguire il codice
	if (_isLogged()) {
		header('Location: ../pages/dashboard.php');
		exit;
	}
	
	// funziona solamente se viene fatta una richiesta post di login
	if (isset($_POST['login'])) {
		$mail 		= $_POST['mail'] ?? '';
		$password 	= $_POST['password'] ?? '';
		
		// se non sono specificati mail e password
		if (empty($mail) || empty($password)) {
			$msg = 'Inserisci username e password %s';
		} 
		
		else {
			
			// preparo la query  per ottenere le credenziali
			$query = "
				SELECT mail, password
				FROM user
				WHERE mail = :mail";


			// ottengo le credenzial
			$check = $pdo->prepare($query);
			$check->bindParam(':mail', $mail, PDO::PARAM_STR);
			$check->execute();
			$user =  $check->fetch(PDO::FETCH_ASSOC);

			// controllo se la password inserita è corretta
			if (!$user || password_verify($password, $user["password"]) === false) {
				//$password_hash = password_hash($password, PASSWORD_BCRYPT);
				$msg = 'Credenziali utente errate %s';
			} 
			
			else {
				// genero un nuovo id di sessione
				session_regenerate_id();
				$_SESSION['session_id'] = session_id();
				$_SESSION['session_username'] = $user['username'];
				$_SESSION['session_mail'] = $user['mail'];

				header('Location: ../pages/dashboard.php');
				exit;
			}
		}
		
		printf($msg, '<a href="../pages/login.php">torna indietro</a>');
	}

?>

