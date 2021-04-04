<?php

	// carica le configurazioni del sito
	$config = include("./config.php");

	// si connette al database
	$db_config = $config['db_engine'] . ":host=".$config['db_host'] . ";dbname=" . $config['db_name'];

	try {
		$pdo = new PDO($db_config, $config['db_user'], $config['db_password'], [
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
		]);
			
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	} 
	
	// caso di errore
	catch (PDOException $e) {
		exit("Impossibile connettersi al database: " . $e->getMessage());
	}


	function &getCredentials($pdo){

		$query = "
				SELECT mail, password
				FROM user
				WHERE mail = :mail";

		$check = $pdo->prepare($query);
		$check->bindParam(':mail', $mail, PDO::PARAM_STR);
		$check->execute();

		$user =  $check->fetch(PDO::FETCH_ASSOC);
		echo var_dump($user);
		return $user;
	}
?>