<?php
require("utils.php");
require_once('database.php');


if (isset($_POST['signin'])) {

	// recupero mail, password e username
    $mail     = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = $_POST['username'] ?? '';

   	$isUsernameValid = true;
	$pwdLenght = mb_strlen($password);


    
	// controllo se i campi sono vuoti
    if (empty($mail) || empty($password)) {
        /*$msg = 'Compila tutti i campi %s';*/
		header("location: ../pages/signin.php?code=-2");
    } 
	
	elseif (false === $isUsernameValid) {
        /*$msg = 'L\'username non è valido. Sono ammessi solamente caratteri 
                alfanumerici e l\'underscore. Lunghezza minina 3 caratteri.
                Lunghezza massima 20 caratteri %s';*/
		header("location: ../pages/signin.php?code=-2");
    } 
	
	// controllo la lunghezza della password
	elseif ($pwdLenght < 8 || $pwdLenght > 20) {
        /*$msg = 'Lunghezza minima password 8 caratteri.
                Lunghezza massima 20 caratteri %s';*/
		header("location: ../pages/signin.php?code=-2");
    } 
	
	else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $query = "
            SELECT mail
            FROM user
            WHERE mail = :mail
        ";
        
        $check = $pdo->prepare($query);
        $check->bindParam(':mail', $mail, PDO::PARAM_STR);
        $check->execute();
        
        $user = $check->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($user) > 0) {
           // $msg = 'Mail già in uso %s';
        } 
		
		else {
            $query = "
                INSERT INTO user (mail, password, username)
                VALUES (:mail, :password, :username)
            ";
        
            $check = $pdo->prepare($query);
            $check->bindParam(':mail', $mail, PDO::PARAM_STR);
            $check->bindParam(':password', $password_hash, PDO::PARAM_STR);
			$check->bindParam(':username', $username, PDO::PARAM_STR);

            $check->execute();
            
            if ($check->rowCount() > 0) {
                //$msg = 'Registrazione eseguita con successo %s';
				//header("location: ../pages/login.php");
				header("location: ../pages/signin.php?code=1");

            } else {
                //$msg = 'Problemi con l\'inserimento dei dati %s';
				header("location: ../pages/signin.php?code=0");
            }
        }
    }
    
	//printf($msg, '<a href="../pages/signin.php">torna indietro</a>');
}