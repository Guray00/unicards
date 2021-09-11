<?php
	require("../php/utils.php");
	require_once("../php/database.php");



	// controllo di aver inserito id e utente per individuare un mazzo
	if 	( 	!isset($_SESSION["match_deck_id"]) or
			!isset($_SESSION["match_deck_owner"]) or
			!isset($_SESSION["match_mode"]) or
			!isset($_SESSION["match_deck_id"]) or 
			$_SESSION["match_logged"]
		){
		// non mette le carte
	}


	else {
		// imposto le variabili con quanto passato in post
		$deck = $_SESSION["match_deck_id"];
		$user = $_SESSION["match_deck_owner"];
		$mode = $_SESSION["match_mode"];
		$match = $_SESSION["match_id"];
		$_SESSION["match_logged"] = true;


		// prendo l'elenco delle carte
		$query="
			select S.name as section, C.question, C.id, C.type
			from card C, section S, Deck D
			where C.id = S.card_id and S.deck_id = :deck and S.deck_id = D.id and D.user = :user order by S.name, rand()";


		$q1 = $pdo->prepare($query);
		$q1->bindParam(':deck', $deck, PDO::PARAM_STR);
		$q1->bindParam(':user', $user, PDO::PARAM_STR);
		$q1->execute();
		$cards =  $q1->fetchAll(PDO::FETCH_ASSOC);

	}

	/* INIZIO SVILUPPO */


	// gestisce il menu di navigazione tra le carte
	function create_navigator($index, $cards){

		global $mode;

		$n = 0;

		// conteggio delle carte effettive
		foreach ($cards as $s){
			if ($mode == 0 && $s["type"] >= 0){
				$n++;
			}

			else if ($mode > 0 && $s["type"] > 0){
				$n++;
			}
		}

		$size = 100/$n;
		$class = "first";
		

		for ($i=1; $i <= $n; $i++){
			$color = "";
			$selected = "";

			if($i==$n && $i==1){$class = "first last";}
			else if($i==$n && $i != 1) $class = "last";

			if ($i%2==0) 
				$color = "color2";

			else
				$color = "color1";

			if($i == $index){
				$selected="selected";
			}


			// stampa il riquadro del menu (navigator itemx\)
			echo "<div class='navigator-item {$class} {$color} {$selected}' id='navigator{$i}' style='width: {$size}%;' onclick='goToAnswer({$i})'>{$i}</div>";
			$class = "";
		}
	}


	function makeMultichoice($card){

		global $pdo;

		$query="
		select A.answer, A.id
		from card C, answers A
		where C.id = A.card_id and C.id = :cardid
		order by rand()";


		$q = $pdo->prepare($query);
		$q->bindParam(':cardid', $card["id"], PDO::PARAM_STR);
		$q->execute();
		$answ =  $q->fetchAll(PDO::FETCH_ASSOC);


		$tot = "";

		foreach($answ as $i => $a){
			
			$tot = $tot . "<div class='content-box answer-content' id='{$a["id"]}' onclick='chooseAnswer(this)'>
					<p>{$a["answer"]}</p>
				</div>";
		}

		if ($card["type"] == 2){
			$tot .= "<div class='warning'>Più risposte potrebbero essere corrette!</div>";
		}
		return $tot;
	}


	/*
	 * Il seguente codice si occupa di creare il menu contenente la domanda
	 * e i pulsanti di gioco.
	 */
	function makeQuestionSide($index, $card, $size){

			global $mode, $pdo;

			$onclick="onclick='sendAnswer(this)'";
			$load_answer_if_sp = "";

			// se siamo in singolo giocatore
			if ($mode == 0 && $card["type"] == 0){	
				$query = "select answer from answers where card_id=:id limit 1";
				$request = $pdo->prepare($query);
				$request->bindParam(':id', $card["id"], PDO::PARAM_STR);
				$request->execute();
				$answ = $request->fetch()["answer"];

				$onclick = "onclick='spShowAnswer(this, \"{$answ}\")'";
				//$load_answer_if_sp = "<div class='sp-answer'><h1>Risposta: {$index}</h1><p>{$answ}</p></div>";
			}
		
				echo"	<div class='navigator'>";
							create_navigator($index, $size);

				echo "	</div>
						<!-- container della domanda -->
						<div class='content-box card-content'>
							<h1>Domanda: {$index}</h1>
							<p>{$card["question"]}
							</p>
						</div>

						<!-- pulsanti di gioco -->
						<div class= 'buttons-box'>
							<div class='back'>
							</div>
							<div class='play' {$onclick}>
							</div>
							<div class='skip'>
							</div>
						</div>

						<!-- pulsante di fine partita -->
						<div class='finish' onclick='submit()'>
						</div>
					
						";
	}

	/*
	<div class='lbl_match_id'>
								#{$_SESSION["match_id"]}
						</div>
	*/


	/* funzione che si occupa di creare i div per tutte le pagine */
	function loadCards(){
		global $cards, $mode;

		/**
		 * 	Le carte di tipo 0, ovvero con singola risposta, non sono
		 *  consentite all'interno delle partite multigiocatore
		 *  in quanto non è possibile assicurare un risultato randomico
		 *  soddisfacente in un numero elevato di casi. Per tale motivo
		 *  vengolo generate unicamente nelle partite singolo giocatore
		 *  ove la modalità è 0.
		 */


		/* scorro tutte le carte */

		$already_enabled = 0;
		$count = 1;
		foreach($cards as $i => $card){

			
			// se sto valutando una carta a scelta multipla
			if ($card["type"] > 0){
				
				/* abilito la prima carta che trovo alla visualizzazione */
				$enabled = (!$already_enabled) ? "card-enabled" : "";
				$already_enabled = 1;

				echo "<div class='multi-page {$enabled}' id='card{$card["id"]}'>
						<div class='left'>";
							makeQuestionSide(($count), $card, $cards);
				echo "	</div>";
				echo"	<div class='right'>";
				echo 		makeMultichoice($card);
				echo "	</div>";
				echo "</div>";
			}

			else {

				// impedisce l'uscita di domande a risposta singola nel multigiocatore
				if ($mode > 0) continue;


				// se è la prima carta, la rendo attiva
				$enabled = (!$already_enabled) ? "card-enabled" : "";
				$already_enabled = 1;

				echo "<div class='card-page {$enabled}' id='card{$card["id"]}'>

						<!-- serve per la griglia -->
						<div></div>

						<div class='single-center'>";
							makeQuestionSide(($count), $card, $cards);
				echo "	</div>";




				echo"	<!-- selettore scelta per mode=0 -->
						<div  class='single-right'>
							<div class='good_answer' onclick='setSpCorrect(this)'>
							</div>

							<div class='bad_answer'	onclick='setSpWrong()'>
							</div>
					";

				
				echo "	</div>";
				echo "</div>";

			}
			$count++;
		}
	}
	
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
		<script type="text/javascript" src="../js/alertbox.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>
	
	<body>

		<div id="cards-container">
			<?php 
				global $cards;
				if(isset($cards))
					loadCards();
			?>
		</div>

		<div class='lbl_match_id'>
			<?php echo "#{$_SESSION["match_id"]}"; ?>
		</div>
	</body>
</html>