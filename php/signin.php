<?php
require("utils.php");
require_once('database.php');

if (isset($_POST['signin'])) {
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';

    //$isUsernameValid = _isUsernameValid($mail);
   @$isUsernameValid = true;
	$pwdLenght = mb_strlen($password);
    
    if (empty($mail) || empty($password)) {
        $msg = 'Compila tutti i campi %s';
    } 
	
	elseif (false === $isUsernameValid) {
        $msg = 'Lo username non è valido. Sono ammessi solamente caratteri 
                alfanumerici e l\'underscore. Lunghezza minina 3 caratteri.
                Lunghezza massima 20 caratteri %s';

    } 
	
	elseif ($pwdLenght < 8 || $pwdLenght > 20) {
        $msg = 'Lunghezza minima password 8 caratteri.
                Lunghezza massima 20 caratteri %s';
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
            $msg = 'Mail già in uso %s';
        } else {
            $query = "
                INSERT INTO user (mail, password)
                VALUES (:mail, :password)
            ";
        
            $check = $pdo->prepare($query);
            $check->bindParam(':mail', $mail, PDO::PARAM_STR);
            $check->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                $msg = 'Registrazione eseguita con successo %s';
            } else {
                $msg = 'Problemi con l\'inserimento dei dati %s';
            }
        }
    }
    
	printf($msg, '<a href="../pages/signin.php">torna indietro</a>');
}