<?php
	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");

	if(!isset($_POST["id"]) || !isset($_POST["card_id"])){
		echo "error";
		exit(-1);
	}

	$id = $_POST["id"];
	$card_id = $_POST["card_id"];

	// di default si da per scontato che non sia selezionata una risposta
	$answer = -1;

	// se è selezionata una risposta, restituisco se questa è positiva o meno
	if ($id != -1){

		// segna la risposta
		$query = "insert into points(match_id, user, card_id, answer_id, time) values(:match, :user, :card_id, :answer_id, CURRENT_TIMESTAMP);";
		$request = $pdo->prepare($query);
		$request->bindParam(':match', $_SESSION["match_id"], PDO::PARAM_STR);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':card_id', $card_id, PDO::PARAM_STR);
		$request->bindParam(':answer_id', $id, PDO::PARAM_STR);
		$request->execute();

		$query = "select correct from answers where id=:id";
		$request = $pdo->prepare($query);
		$request->bindParam(':id', $id, PDO::PARAM_STR);
		$request->execute();
		$answer = $request->fetch()["correct"];
	}

	else {

		// controlla se abbiamo messo tutte le corrette
		/*$query = "	
					select sum(corrette) as corrette, sum(inserite) as inserite
					from
					(
						(
							select 0 as corrette, count(C.id) as inserite
							from points P, answers C
							where
							P.card_id = C.card_id and C.correct = 1 and C.card_id = :ida and P.user = :user and P.match_id = :match
						)
						
						UNION ALL
						(
							select count(id) as corrette, 0 as inserite
							from answers A
							where 
								A.card_id = :idb and
								A.correct = 1
						)
					) as D
		;";

		$request = $pdo->prepare($query);
		$request->bindParam(':match', $_SESSION["match_id"], PDO::PARAM_STR);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':ida', $card_id, PDO::PARAM_STR);
		$request->bindParam(':idb', $card_id, PDO::PARAM_STR);
		$request->execute();
		$a = $request->fetch();

		var_dump($a);
		

		// se le abbiamo messe tutte, usciamo
		if ($a["corrette"] == $a["inserite"]) return;*/


		// in caso contrario aggiungo un null per segnalare l'errore
		$query = "insert IGNORE into points(match_id, user, card_id, answer_id, time) values(:match, :user, :card_id, null, CURRENT_TIMESTAMP);";
		$request = $pdo->prepare($query);
		$request->bindParam(':match', $_SESSION["match_id"], PDO::PARAM_STR);
		$request->bindParam(':card_id', $card_id, PDO::PARAM_STR);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->execute();
	}
	

	// devo segnare come che ho già risposto alla domanda


	echo $answer;
?>