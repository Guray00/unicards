<?php
	require_once("../php/database.php");

	$name = "Universita di Pisa2";
	$query = "INSERT INTO school (name) VALUES (?)";
	
	//INSERT INTO table (id, name, age) VALUES(1, "A", 19) 
	//	ON DUPLICATE KEY UPDATE  name="A", age=19


	try {
		//$pdo->prepare($query)->execute([$name]);
		echo var_dump($_POST);
	}

	catch(Exception $e){
		//ritorna all'utente che c'è stato un errore con i campi
		header("HTTP/1.1 400");
	}
?>