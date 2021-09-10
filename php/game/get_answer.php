<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	$id = $_POST["id"];
	

	// DEVO CONTROLLARE CHE L'UTENTE ABBIA GIA RISPOSTO ALLA DOMANDA
	$query = "select id, correct from answers where card_id=:id";
		
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $id, PDO::PARAM_STR);
	$request->execute();
	$answers = $request->fetchAll(PDO::FETCH_ASSOC);;
	

	echo json_encode($answers);
?>