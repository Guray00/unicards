<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	if(!isset($_POST["id"])){
		echo "error";
		exit(-1);
	}

	// id della carta di cui si cerca risposta
	$id = $_POST["id"];
	

	// recupero le risposte relative alla domanda appena posta
	$query = "select id, correct from answers where card_id=:id";
		
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $id, PDO::PARAM_STR);
	$request->execute();
	$answers = $request->fetchAll(PDO::FETCH_ASSOC);
	

	echo json_encode($answers);
?>