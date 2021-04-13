<?php
	require("../php/utils.php");
	_sessionCheck();

	require_once("./database.php");


	$owner = $_POST["owner"];
	$deck  = $_POST["deck"];
	
	$query = "delete from favourite where user= :user and deck = :deck and owner = :owner";
		
	$request = $pdo->prepare($query);
	$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
	$request->bindParam(':deck', $deck, PDO::PARAM_STR);
	$request->bindParam(':owner', $owner);
	
	$request->execute();
	

?>