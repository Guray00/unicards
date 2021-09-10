<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	// 
	$card_id = $_POST["card_id"];

	// recuperiamo l'id della risposta associata alla domanda
	// con singola risposta da cui è partita la richiesta.
	// Per assicurarsi, almeno parzialmente, di evitare un cattivo uso
	// da mal intenzionati, si ordina per correttezza di risposta passando
	// dunque sempre la prima risposta in ordine di id a partire da quella sbagliata

	$query = "
		select A.id from answers A, card C 
		where A.card_id=C.id and C.id=:id 
		order by A.correct 
		limit 1;
	";

	$request = $pdo->prepare($query);
	$request->bindParam(':id', $card_id, PDO::PARAM_STR);
	$request->execute();
	$answer_id = $request->fetch()["id"];
	
	
	$query = "
		insert into points values(:match, :user, :id, CURRENT_TIMESTAMP);
	";
	$request = $pdo->prepare($query);
	$request->bindParam(':match', $_SESSION["match_id"], PDO::PARAM_STR);
	$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
	$request->bindParam(':id', $answer_id, PDO::PARAM_STR);

	$a = $request->execute();

	echo $a;
?>