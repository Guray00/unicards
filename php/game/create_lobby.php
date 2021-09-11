<?php

	require("../utils.php");
	_sessionCheck();

	require_once("../database.php");


	$deck = $_POST["id"];
	$user = $_POST["owner"];
	$mode = $_POST["mode"];
	
	$fast_start = $_POST["status"];


	// prendo il tempo per domanda e per risposta
	$query= "select tpq, tpa from deck where id=:id;";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $deck, PDO::PARAM_STR);
	$request->execute();
	$times = $request->fetch();


	// recupero le carte per il conteggio del tempo
	$query="
		select S.name as section, C.question, C.id, C.type
		from card C, section S, Deck D
		where C.id = S.card_id and S.deck_id = :deck and S.deck_id = D.id and D.user = :user order by S.name, rand()";


	$q1 = $pdo->prepare($query);
	$q1->bindParam(':deck', $deck, PDO::PARAM_STR);
	$q1->bindParam(':user', $user, PDO::PARAM_STR);
	$q1->execute();
	$cards =  $q1->fetchAll(PDO::FETCH_ASSOC);


	//conto il numero di domande per saper stabilire una scadenza
	$counter = 0;
	foreach($cards as $i => $card){
		if($card["type"] == "0" && $mode == 0){
			$counter++;
		}

		else if ($card["type"] > 0 ){
			$counter++;
		}
	}

	if (!$fast_start) $finish = null;


	$finish = time() + ($times["tpq"]*$counter + $times["tpa"]*$counter);

	$match_id = "";
	$found = 0;
	do {
		// generiamo un id per il match
		$match_id = strtoupper(substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 4));

		// prendo il tempo per domanda e per risposta
		$query= "select 1 as found from `match` where id=:id";
		$request = $pdo->prepare($query);
		$request->bindParam(':id', $match_id, PDO::PARAM_STR);
		$request->execute();
		$found = count($request->fetchAll(PDO::FETCH_ASSOC));

	// continuiamo a generarlo fino a quando lo troviamo
	}while($found > 0);
	



	$query="
			insert into `match` (id, `mode`, `deck_id`, `owner`, `finish`, `status`) values (:id, :mode, :deck, :owner, FROM_UNIXTIME(:finish), :status);";

	$q1 = $pdo->prepare($query);
	$q1->bindParam(':id', $match_id, PDO::PARAM_STR);
	$q1->bindParam(':deck', $deck, PDO::PARAM_STR);
	$q1->bindParam(':finish', $finish);
	$q1->bindParam(':owner', $user, PDO::PARAM_STR);
	$q1->bindParam(':status', $fast_start, PDO::PARAM_STR);
	$q1->bindParam(':mode', $mode, PDO::PARAM_STR);
	$q1->execute();

	$_SESSION["match_deck_id"] = $deck;
	$_SESSION["match_deck_owner"] = $user;
	$_SESSION["match_mode"] = $mode;
	$_SESSION["match_id"] = $match_id;
	$_SESSION["match_logged"] = false;

	echo $match_id;

?>