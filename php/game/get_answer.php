<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	if(!isset($_POST["id"])){
		echo "error";
		exit(-1);
	}

	$id = $_POST["id"];
	
	// prima di verificare le giuste, controllo che l'utente
	// abbia realmente risposto alla domanda
	/*$query = "select 1 as answer from points where card_id=:id and match_id=:match and user=:user ;";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $id, PDO::PARAM_STR);
	$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
	$request->bindParam(':match', $_SESSION["match_id"], PDO::PARAM_STR);
	$request->execute();
	$found = count($request->fetchAll(PDO::FETCH_ASSOC));

	// se non è stata trovata nessuna domanda
	if($found < 1) exit(-1);*/

	$query = "select id, correct from answers where card_id=:id";
		
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $id, PDO::PARAM_STR);
	$request->execute();
	$answers = $request->fetchAll(PDO::FETCH_ASSOC);
	

	echo json_encode($answers);
?>