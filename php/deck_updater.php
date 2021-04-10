<?php
	require_once("../php/database.php");

	try {		
		$deck  = $_POST['deck'];
		$cards = $_POST['cards'];

		$deck["public"] = strtoupper($deck["public"]) == "TRUE" ? TRUE : FALSE;

		// aggiorno le informazioni del deck
		if (strtoupper($deck["school"]) == "NULL") $deck["school"] = NULL;

		$query = 'select deckUpdater(:id, :user, :name, :school, NULL, :public, :color)';
		$params = ['id' 	  => $deck["id"], 	  'name'   => $deck["name"], 
					'user' 	  => $deck["user"],   'school' => $deck["school"],
					'public'  => $deck["public"],  'color' => $deck["color"]];
		//$response = $pdo->prepare($query)->execute($params);
		
		$run = $pdo->prepare($query);
		$run->execute($params);
		$response =  $run->fetch(PDO::FETCH_ASSOC);

		if($response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"] > 0){
			$deck["id"] = $response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"];
		}

		// rimuovo tutte le sezione e le carte connesse al deck
		$query = "DELETE FROM card
				  WHERE id in (SELECT card_id as id FROM section where deck_id=:deck);";

		$params = ['deck' => $deck["id"]];
		$pdo->prepare($query)->execute($params);
		

		foreach($cards as $section_name => $section){
			foreach ($section as $question => $answer){
				$query = "REPLACE INTO card values (NULL, :question, :answer);";
				$params = ['question' => $question, 'answer' => $answer];
				$pdo->prepare($query)->execute($params);
				$card_id = $pdo->lastInsertId();

				$query = "REPLACE INTO section values (:deck, :user, :card , :section);";
				$params = ['deck' => $deck["id"], 'user' => $deck["user"], 'card' => $card_id, 'section' => $section_name];
				$pdo->prepare($query)->execute($params);
			}
		}

		echo $response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"];
	}

	catch(Exception $e){
		//ritorna all'utente che c'è stato un errore con i campi
		header("HTTP/1.1 400");
	}
?>