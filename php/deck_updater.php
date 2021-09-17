<?php
	/*
		*	Il seguente file viene utilizzato per gestire l'aggiornamento
		*	dei mazzi in base a delle richieste post eseguite con ajax
		*	dal file deck_editor.js
	*/

	require_once("../php/database.php");


	// sistemo i valori dei parametri
	function parameters_clean_up(){

		// mi consente di modificare il valore preso in post
		global $deck;

		// eseguo il trim del valori per rimuovere spazi bianchi prima e dopo
		foreach ($deck as $key => $value){
			$deck[$key] = trim($value);
		}

		// rendo la prima lettera del nome di un mazzo uppercase
		$deck["name"] = ucfirst($deck["name"]);

		// converto le stringhe false e true in valori, necessario per mysql
		$deck["public"] = strtoupper($deck["public"]) == "TRUE" ? TRUE : FALSE;

		// aggiorno le informazioni del deck
		if (strtoupper($deck["school"]) == "NULL") $deck["school"] = NULL;
	}

	// aggiorno la tabella deck
	function update_deck_table(){
		
		global $deck, $cards, $pdo;

		$query = 'select deckUpdater(:id, :user, :name, :school, NULL, :public, :color)';
		$params = ['id' 	  => $deck["id"], 	  'name'   => $deck["name"], 
					'user' 	  => $deck["user"],   'school' => $deck["school"],
					'public'  => $deck["public"],  'color' => $deck["color"]];
		
		$run = $pdo->prepare($query);
		$run->execute($params);
		$response =  $run->fetch(PDO::FETCH_ASSOC);

		if($response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"] > 0){
			$deck["id"] = $response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"];
		}

		return $response["deckUpdater(?, ?, ?, ?, NULL, ?, ?)"];
	}


	// rimuovo le carte e le sezioni già presenti
	function remove_cards_and_sections(){

		global $deck, $pdo;

		$query = "DELETE FROM card
		WHERE id in (SELECT card_id as id FROM section where deck_id=:deck);";

		$params = ['deck' => $deck["id"]];
		$pdo->prepare($query)->execute($params);
	}

	// inserisco le carte e le sezioni nuove
	function insert_cards_and_section(){

		global $cards, $deck, $pdo;

		

		foreach($cards as $section_name => $section){

			foreach ($section as $question => $answer){


				// eseguo il trim per la rimozione dis spazi e rendo maiuscola la prima lettera
				$question = ucfirst(trim($question));


				$query = "REPLACE INTO card values (NULL, :question, :mod);";
				$params = ['question' => $question, 'mod' => $answer[0]];
				$pdo->prepare($query)->execute($params);
				$card_id = $pdo->lastInsertId();


				foreach($answer[1] as $y => $x){


					$a = ucfirst(trim($x[0]));
					$c = $x[1];

					$query = "REPLACE INTO answers values (:cardid, NULL, :answer, :correct);";
					$params = ['cardid'=>$card_id, 'answer' => $a, 'correct' => $c];
					$pdo->prepare($query)->execute($params);
				}

				$query = "REPLACE INTO section values (:deck, :user, :card , :section);";
				$params = ['deck' => $deck["id"], 'user' => $deck["user"], 'card' => $card_id, 'section' => $section_name];
				$pdo->prepare($query)->execute($params);				
			}
		}
	}

	/**********************************************
	 *                                            *
	 *                     MAIN                   *
	 *                                            *
	***********************************************/

	try {		
		$deck  = $_POST['deck'];
		$cards = $_POST['cards'];

		// sanifico i parametri passati
		parameters_clean_up();

		// aggiorno la tabella deck e salvo la risposta del database
		$response = update_deck_table();
		

		// rimuovo tutte le sezione e le carte connesse al deck
		remove_cards_and_sections();
		
		// aggiungo le nuove carte
		insert_cards_and_section();
		

		// ritorno -1 se non è stato fatto nulla, 0 se è un aggiornamento
		// o un numero maggiore o uguale a 1 per indicare l'id in cui è stato
		// inserito il mazzo, in modo da consentire il reload della pagina
		echo $response;
	}

	catch(Exception $e){
		//ritorna all'utente che c'è stato un errore con i campi
		echo $e;
		header("HTTP/1.1 400");
	}
?>