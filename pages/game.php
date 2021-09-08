<?php
	require("../php/utils.php");
	require_once("../php/database.php");

	// gestisce il menu di navigazione tra le carte
	function create_navigator($n){
		$size = 100/$n;
		$class = "first selected";
		

		for ($i=1; $i <= $n; $i++){
			$color = "";

			if($i==$n){$class = "last";}

			// gestisce il colore del menu
			if ($i != 1) {$color = "background-color:#383e49;";}
			if ($i%2 == 0 ){$color = "background-color:#3E4450;";}


			// stampa il riquadro del menu (navigator itemx\)
			echo "<div class='navigator-item {$class}' id='{$i}_navigator' style='width: {$size}%; {$color}'>{$i}</div>";
			
			$class = "";
		}
	}
	
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
		select S.name as section, C.question
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

	/* 	
		inserisco un div dove cambio man mano che premo il contenuto
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
		<link rel="stylesheet" href="../css/layout/2-column-layout.css">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<script type="text/javascript" src="../js/game.js"></script>
	</head>
	
	<body>

		<div class="multi-page">
			<div class="left">
				
				<div class="navigator">
					<?php create_navigator(20);?>
				</div>

				<div class="content-box card-content">
						<h1>Domanda: 1</h1>

						<p>	Questa è la domanda importante che ti sto 
							che necessita di una risposta possibilmente
							responsive il prima possibile.
						</p>
					
				</div>

				<div class= "buttons-box">
					<div class="back">
					</div>
					<div class="play">
					</div>
					<div class="skip">
					</div>
				</div>

			</div>
			
			<div class="right">
			
				<div class="content-box answer-content">
					<p>	Questa è la domanda importante che ti sto 
						che necessita di una risposta possibilmente
						responsive il prima possibile.
					</p>
				</div>

				<div class="content-box answer-content">
					<p>	Hallo Lorem ipsum dolor sit amet, amet luctus lacinia at, purus sed massa mattis suspendisse adipiscing, odio massa phasellus vestibulum porttitor, dolor nulla nec porttitor rhoncus ullamcorper amet. Nam in, eget nisl aliquet fames placerat morbi, ante porta lacus nibh, semper maecenas vehicula dui feugiat ut, massa per nonummy cras euismod praesent phasellus. Augue pulvinar mattis, sed nostra sed metus donec, lacus neque quis est praesent nunc ante, pede in ac ut, eget sed. Nunc feugiat quam magna lacinia.
					</p>
				</div>

				<div class="content-box answer-content">
					<p>	Questa è la domanda importante che ti sto 
						che necessita di una risposta possibilmente
						responsive il prima possibile.
					</p>
				</div>

				<div class="content-box answer-content">
					<p>	Questa è la domanda importante che ti sto 
						che necessita di una risposta possibilmente
						responsive il prima possibile.
					</p>
				</div>

				<div class="warning">Più risposte potrebbero essere corrette!</div>

			</div>
		
			</div>
		</div>

	</body>
</html>