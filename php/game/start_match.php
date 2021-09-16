<?php

	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");

	// recupero id del match
	$match = $_POST["id"];

	// recupero il master del match
	$query= "select `master` from `match` where id = :id;";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$master = $request->fetch();

	// mi assicuro che il master esista
	if(isset($master) && isset($master["master"])) $master = $master["master"];
	else {
		header("HTTP/1.1 400");
	}

	// leva i non autorizzati
	if($master != $_SESSION["session_mail"]) header("HTTP/1.1 400");

	
	// definisco la partita come iniziata
	$query= "update `match`  set `status` = 1 where id = :id;";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$times = $request->fetch();
	
?>