<?php

	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	$match = $_POST["id"];


	$query= "select status from `match` where id = :id";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$status = $request->fetch()["status"];

	echo $status;
?>