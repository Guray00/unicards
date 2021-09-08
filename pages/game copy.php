<?php
	require("../php/utils.php");
	require_once("../php/database.php");
	
	/*
	
		prendo dal database un elenco di domande, risposte e e sezioni
	
		// creo un elenco di elementi per sezioni

		nella modalità multiplayer faccio una chiamata ajax che in base al tempo
		di inizio della partita calcola la domanda che attualmente è mostrata a 
		tutti i giocatori
	*/

//	echo var_dump($_GET);
	// controllo di aver inserito id e utente per individuare un mazzo
	if (isset($_GET["id"]) != true or isset($_GET["user"]) != true){
		echo "error loading";
		exit();
	}


	$query="
		select S.name as section, C.question, C.answer
		from card C, section S, Deck D
		where C.id = S.card_id and S.deck_id = :deck and S.deck_id = D.id and D.user = :user order by S.name, rand()";


	$deck = 2;
	$user = "test@test.it";

	$q1 = $pdo->prepare($query);
	$q1->bindParam(':deck', $deck, PDO::PARAM_STR);
	$q1->bindParam(':user', $user, PDO::PARAM_STR);
	$q1->execute();
	$cards =  $q1->fetchAll(PDO::FETCH_ASSOC);


	$result = array();
	foreach ($cards as $element) {
		$result[$element['section']][] = $element;
	}

	$card_list = json_encode($result);

	/* 	inserisco un div dove cambio man mano che premo il contenuto
	 	e con un indice tengo traccia della domanda sulla quale stiamo
		attualmente navigando. E' meglio di creare tutti i div perchè 
		sennò l'utente finale potrebbe barare navigando tra le domande.
	*/
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Unicards</title>
		<link rel="stylesheet" href="../css/layout/game.css">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<script type="text/javascript" src="../js/game.js"></script>
	</head>
	
	<body>
		<div id="test">
			<div id="card-progress" class="content-box">
				card progress
			</div>

			<div id="card-content" class="content-box">
				<h1>Domanda: 5</h1>

				<p>	Questa è la domanda importante che ti sto 
					che necessita di una risposta possibilmente
					responsive il prima possibile.
				</p>
			</div>

			<div id="button-menu">
				<button type="button">Indietro</button>
				<button type="button">Avanti</button>
				<button type="button">Salta</button>
			</div>
		</div>
		
	</body>
</html>