<?php
	require("../php/utils.php");
	_sessionCheck();

	require_once("./database.php");


	$owner = $_POST["owner"];
	$deck  = $_POST["deck"];
	$public = 1;

	if ($owner != $_SESSION["session_mail"]){
		$query = "select public from deck where id= :deck and user= :user";
		$request = $pdo->prepare($query);	
		$request->bindParam(':user', $owner);
		$request->bindParam(':deck', $deck, PDO::PARAM_STR);
		$request->execute();
		$public =  $request->fetch(PDO::FETCH_ASSOC);

		$public = $public["public"];
	}

	if ($public == 1){
		$query = "replace into favourite(user, deck, owner) values (:user, :deck, :owner)";
			
		$request = $pdo->prepare($query);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':deck', $deck, PDO::PARAM_STR);
		$request->bindParam(':owner', $owner);
		
		$request->execute();
	}

?>