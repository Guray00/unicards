<?php

	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	// ci procuriamo le informazioni sulla partita
	$match = $_POST["id"];
	$user  = $_SESSION["session_mail"];

	// eliminiamo il giocatore dalla tabella giocatori
	$query= "delete from `player` where `match_id` = :match and user = :user";
	$request = $pdo->prepare($query);
	$request->bindParam(':match', $match, PDO::PARAM_STR);
	$request->bindParam(':user',   $user, PDO::PARAM_STR);
	$a = $request->execute();

	echo $a;
?>