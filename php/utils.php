<?php
	session_start();

	//controlla se l'utente ha effettuato l'accesso
	function _sessionCheck(){
		if(!isset($_SESSION["session_id"])){
			header("Location: ../pages/session_timeout.html");
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

?>