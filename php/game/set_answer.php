<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	$id = $_POST["id"];

	// di default si da per scontato che non sia selezionata una risposta
	$answer = -1;

	// se è selezionata una risposta, restituisco se questa è positiva o meno
	if ($id != -1){
		$query = "select correct from answers where id=:id";
		$request = $pdo->prepare($query);
		$request->bindParam(':id', $id, PDO::PARAM_STR);
		$request->execute();
		$answer = $request->fetch()["correct"];
	}
	

	// devo segnare come che ho già risposto alla domanda

	

	echo $answer;
?>