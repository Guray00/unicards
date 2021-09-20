<?php
require("utils.php");
require_once('database.php');


/*
	funzione che consente di inviare un'immagine al server
	e salvarla al suo interno, in modo da poterla recuperare
	in seguito nelle pagine che seguono.
*/


// https://stackoverflow.com/questions/15153776/convert-base64-string-to-an-image-file
function base64_to_jpeg($base64_string, $output_file) {
    
	// apre in lettura il file
    $ifp = fopen( $output_file, 'wb' ); 

    // divide il contenuto della stringa per eliminare il tag
    $data = explode( ',', $base64_string );

    // scrive nel file
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // chiude il file
    fclose( $ifp ); 

	// restituisce il file
    return $output_file; 
}



if (isset($_POST['signin'])) {

	// recupero mail, password e username
    $mail     = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $username = $_POST['username'] ?? '';

   	$isUsernameValid = true;
	$pwdLenght = mb_strlen($password);


    
	// controllo se i campi sono vuoti
    if (empty($mail) || empty($password)) {
		header("location: ../pages/signin.php?code=-2");
    } 
	
	elseif (false === $isUsernameValid) {
		header("location: ../pages/signin.php?code=-2");
    } 
	
	// controllo la lunghezza della password
	elseif ($pwdLenght < 8 || $pwdLenght > 20) {
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
		   header("location: ../pages/signin.php?code=0");
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

				// carica la foto
				if ($_POST["upload"] != "")
					base64_to_jpeg($_POST["upload"], "../assets/users/".$mail);

				// rimanda alla pagina confermando l'avvenuto
				header("location: ../pages/signin.php?code=1");

            } else {
                //$msg = 'Problemi con l\'inserimento dei dati %s';
				header("location: ../pages/signin.php?code=0");
            }
        }
    }
}