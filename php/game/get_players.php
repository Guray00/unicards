<?php

	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	$match = $_POST["id"];

	// recupero la lista degli utenti della lobby attuale
	$query= "select user as id, username from player P, user S where match_id = :id and P.user = S.mail";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$players = $request->fetchAll(PDO::FETCH_ASSOC);

	echo json_encode($players);
?>