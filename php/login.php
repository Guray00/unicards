
<?php
	require("./utils.php");
	require_once("./database.php");
	
	if (_isLogged()) {
		header('Location: ../pages/dashboard.php');
		exit;
	}
	
	if (isset($_POST['login'])) {
		$mail 		= $_POST['mail'] ?? '';
		$password 	= $_POST['password'] ?? '';
		
		if (empty($mail) || empty($password)) {
			$msg = 'Inserisci username e password %s';
		} 
		
		else {
			$query = "
				SELECT mail, password
				FROM user
				WHERE mail = :mail
			";
			
			$check = $pdo->prepare($query);
			$check->bindParam(':mail', $mail, PDO::PARAM_STR);
			$check->execute();
			$user = $check->fetch(PDO::FETCH_ASSOC);


			if (!$user || password_verify($password, $user["password"]) === false) {
				//$password_hash = password_hash($password, PASSWORD_BCRYPT);
				$msg = 'Credenziali utente errate %s';
			} 
			
			else {
				session_regenerate_id();
				$_SESSION['session_id'] = session_id();
				$_SESSION['session_username'] = $user['username'];
				$_SESSION['mail'] = $user['mail'];

				header('Location: ../pages/dashboard.php');
				exit;
			}
		}
		
		printf($msg, '<a href="../pages/login.php">torna indietro</a>');
	}

?>

