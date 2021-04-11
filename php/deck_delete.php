<?php
	require_once("../php/database.php");

	try {		
		$deck  = $_POST['deck'];
		
		// rimuovo tutte le sezione e le carte e le sezioni connesse al deck
		$query = "DELETE FROM card
				  WHERE id in (SELECT card_id as id FROM section where deck_id=:deck);";

		$params = ['deck' => $deck["id"]];
		$pdo->prepare($query)->execute($params);
		
		$query = "DELETE FROM deck
				  WHERE id=:deck;";

		$params = ['deck' => $deck["id"]];
		$pdo->prepare($query)->execute($params);

		echo "success";
	}

	catch(Exception $e){
		//ritorna all'utente che c'è stato un errore con i campi
		header("HTTP/1.1 400");
	}
?>