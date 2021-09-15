<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");

	if(!isset($_POST["card_id"]) || !isset($_POST["correct"])){
		echo "error";
		exit(-1);
	}

	// recupero id e livello di correttezza
	$card_id = $_POST["card_id"];
	$correct = $_POST["correct"];

	$match   = $_SESSION["match_id"];
	

	// recuperiamo l'id della risposta associata alla domanda
	// con singola risposta da cui è partita la richiesta.
	// Per assicurarsi, almeno parzialmente, di evitare un cattivo uso
	// da mal intenzionati, si ordina per correttezza di risposta passando
	// dunque sempre la prima risposta in ordine di id a partire da quella sbagliata

	// se ho risposto correttamente
	if($correct == "true"){

		// recupero id per la risposta corretta
		$query = "
			select A.id from answers A, card C 
			where A.card_id=C.id and C.id=:id and C.type=0
			order by A.correct 
			limit 1;
		";

		$request = $pdo->prepare($query);
		$request->bindParam(':id', $card_id, PDO::PARAM_STR);
		$request->execute();
		$answer_id = $request->fetch()["id"];

		// inserisco nell'elenco dei punteggi la risposta corretta alla domanda corretta
		$query = "insert into points values(:match, :user, :id, :answer, CURRENT_TIMESTAMP);";
		$request = $pdo->prepare($query);
		$request->bindParam(':match', $match, PDO::PARAM_STR);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':id', $card_id, PDO::PARAM_STR);
		$request->bindParam(':answer', $answer_id, PDO::PARAM_STR);
		$a = $request->execute();
	}

	// se non ho risposto correttamente
	else {
		// inserisco nel punteggio come risposta con NULL ovvero mancante
		$query = "insert into points values(:match, :user, :id, NULL, CURRENT_TIMESTAMP);";
	
		$request = $pdo->prepare($query);
		$request->bindParam(':match', $match, PDO::PARAM_STR);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':id', $card_id, PDO::PARAM_STR);
		$a = $request->execute();

		echo $a;
	}
?>