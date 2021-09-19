<?php
	session_start();

	//controlla se l'utente ha effettuato l'accesso
	function _sessionCheck(){
		if(!isset($_SESSION["session_id"])){
			header("Location: ../pages/login.php");
			exit();
		}
	}


	// restituisce se è stato fatto l'accesso
	function _isLogged(){
		if(!isset($_SESSION["session_id"])){
			return false;
		}

		return true;
	}

	function _isUsernameValid($username){
		return filter_var(
			$username,
			FILTER_VALIDATE_REGEXP, [
				"options" => [
					"regexp" => "/^[a-z\d_@]{3,20}$/i"
				]
			]
		);
	}

?>