<?php
	// controlla se l'utente ha effettuato l'accesso
	require("../php/utils.php");
	require_once("../php/database.php");

	_sessionCheck();

	$currentpage="deck_edit";

	/******************************
	 			FUNZIONI
	 *****************************/

	// restituisce id del deck e mail dell utente
	function submitID(){
		if(isset($_GET["id"])){
			echo $_GET["id"] . ", \"" . $_SESSION["session_mail"] . "\"";
		}

		else 
			echo "\"NULL\"" . ", \"" . $_SESSION["session_mail"] . "\"";
	}

	function echoButtonLastDiv(){
		echo "
		<div class='buttons-last-div'>
			<button type='button' class='btn-remove' id='btn-remove-tab' onclick='removeTab(this)'></button> 
			<button type='button' id='radio-add' onclick='addCard(this, 4)'>Aggiungi scelta multipla</button> 
			<button type='button' id='btn-add' onclick='addCard(this, 1)'>Aggiungi carta</button> 
		</div>
		";
	}

	// contenitore delle tab
	function defaultTabContainer(){
		echo " <div class=\"tab-container\" id='tab1'> 
					<div class='cards-container'>

						<div class='section-info-div'>
							<label id='1_lbl_section'>Nome sezione:</label>
							<input class='section_name' id='1_section_name' type='text' value='Untitled' required oninvalid='onInvalidText()'></input>
						</div>

						<div class='card-div'>
							<label id='1_lbl_question1'>Domanda 1</label>
							<label id='1_lbl_answer1'>Risposta 1</label>
							<textarea id='1_question1' name='1_question1' form='deck_form' required oninvalid='onInvalidText()'></textarea>
							<textarea id='1_answer1' name='1_answer1'   form='deck_form' required oninvalid='onInvalidText()'></textarea>
							<input id='1_type1' name='1_type1' value='0' style='display:none;'  form='deck_form' />
							<input id='1_correct1' name='1_correct1' value='1' style='display:none;'  form='deck_form' />
						</div>";

		echoButtonLastDiv();						

		echo	"		</div>
	 			</div>";
	}

	function defaultTabs(){
		echo "<input type='radio' id='tab1' value='tab1' name='tabs' class='tab' checked/>
			  <label for='tab1' class='tab' id='1_section'>Untitled</label>";
	}

	function loadCards($cards){
		$tab_number = 1;
		foreach ($cards as $section => $value){

			echo "<div class=\"tab-container\" id='tab{$tab_number}'> 
					<div class='cards-container'>

					<div class='section-info-div'>
						<label id='1_lbl_section'>Nome sezione:</label>
						<input class='section_name' id='{$tab_number}_section_name' type='text' value='{$section}' required oninvalid='onInvalidText()'></input>
					</div>
					";

						$question_number = 1;
						foreach($value as $c){

							$lbl_question = $tab_number.'_lbl_question'.$question_number;
							$question = $tab_number.'_question'.$question_number;
							$question_type =  $tab_number.'_type'.$question_number;

							// se la carta è una domanda semplice
							if (count($c["answer"]) == 1){
								$lbl_answer   = $tab_number.'_lbl_answer'.$question_number;
								$answer   = $tab_number.'_answer'.$question_number;
								$correct   	= $tab_number.'_correct'.$question_number;

								echo 	"	
											<div class='card-div' id= 'card-div-{$question_number}'>
												<label 	  id='{$lbl_question}'>Domanda {$question_number}</label>
												<label 	  id='{$lbl_answer}'  >Risposta {$question_number}</label>

												<textarea id='{$question}' name='{$question}' form='deck_form' required oninvalid='onInvalidText()'>{$c['question']}</textarea>
												<textarea id='{$answer}'   name='{$answer}' form='deck_form' required oninvalid='onInvalidText()'>{$c['answer'  ][0][0]}</textarea>
												
												<!-- servono per il form -->
												<input id='{$question_type}' name='{$question_type}' value='0' style='display:none;'  form='deck_form' />
												<input id='{$correct}' name='{$correct}' value='1' style='display:none;'  form='deck_form' />
											</div>
										";
											
								$question_number++;
							}

							else if (count($c["answer"]) == 4){

								//$lbl_question = $tab_number.'_lbl_question'.$question_number;
								//$question = $tab_number.'_question'.$question_number;
								$type_radio = $c["type"] == 1 ? "ctr-radio-selected" : "ctr-radio";
								$type_check = $c["type"] == 2 ? "ctr-check-selected" : "ctr-check";

								
								// disegnamo la domanda e il selettore check / radio
								echo 	"<div class='multichoice-4-div' id= 'card-div-{$question_number}'>
											<label 	  id='{$lbl_question}'>Domanda {$question_number}</label>
											<textarea id='{$question}' name='{$question}' form='deck_form' required oninvalid='onInvalidText()'>{$c['question']}</textarea>

											<div class='check-to-radio'>
											<input type='button' class='{$type_radio}'  onclick='ctr_radio(this);'/>
											<input type='button' class='{$type_check}'  onclick='ctr_check(this);'/>
											
											<!-- serve per il form -->
											<input id='{$question_type}' class='question-type' name='{$question_type}' value='{$c["type"]}' style='display:none;' form='deck_form'/>
										</div>
								";

								$letters = ["A", "B", "C", "D"];
								for($i = 0; $i < 4; $i++){

									$lbl_answer = $tab_number.'_lbl_answer'.$question_number."_".$letters[$i];
									$answer   	= $tab_number.'_answer'.$question_number."_".$letters[$i];
									$correct   	= $tab_number.'_correct'.$question_number."_".$letters[$i];

									$shape = $c["type"] == 1 ? "style='border-radius:50pt;'" : "style='border-radius:2pt;'";
									//style="property:value;"

									$value = $c['answer'  ][$i][1];
									$set_true = $c['answer'  ][$i][1] == 1 ? "set-true-selected" : "set-true";


									echo 	"				
										<label 	  id='{$lbl_answer}'  >{$letters[$i]}</label>
										<textarea id='{$answer}'   name='{$answer}' form='deck_form' required oninvalid='onInvalidText()'>{$c['answer'  ][$i][0]}</textarea>
										<input type='label' class='{$set_true}' id='{$correct}' name='{$correct}' value='{$value}' form='deck_form' {$shape} onclick='set_answer_true(this)'/>
								";
								}

								echo "</div>";

								
											
								$question_number++;
							}

						}
						
						echoButtonLastDiv();
			
			// 		chiude cards-container e tabs-container 
			echo 	"</div>
				</div>";

			$tab_number++;
		}
	}

	function loadTabs($cards){
		$count = 1;
		$checked = "checked";
		foreach ($cards as $section => $value){
			echo "
			<input type='radio' id='tab{$count}' value='tab{$count}' name='tabs' class='tab' {$checked}/>
			<label for='tab{$count}' class='tab' id='{$count}_section'>{$section}</label>
			";							
			$checked = "";
			$count++;
		}
	}

	/******************************
	 			DATABASE
	 *****************************/

	$name   = "";
	$school = "";
	$degree = "";
	$schools = [];
	$color  = "#6188f5";
	$public = 0;
	if (isset($_GET["id"]) == true){
		
		$query = "
				SELECT D.id, D.name, D.school, D.degree, D.public, D.color
				FROM deck D
				WHERE D.user = :mail AND D.id= :id";


		// ottengo le informazioni sul mazzo
		$q1 = $pdo->prepare($query);
		$q1->bindParam(':mail', $_SESSION["session_mail"], PDO::PARAM_STR);
		$q1->bindParam(':id', $_GET["id"], PDO::PARAM_STR);
		$q1->execute();
		$info =  $q1->fetch(PDO::FETCH_ASSOC);

		if (!$info) {
			echo "Non disponi delle credenziali per modificare questo mazzo";
			exit();
		}
		
		$name 	 = $info["name"];
		$school  = strtoupper($info["school"]) != "NULL" ? $info["school"] : "";
		$degree  = strtoupper($info["degree"]) != "NULL" ? $info["degree"] : "";
		$color   = $info["color"];
		$deck_id = $_GET["id"];
		$public  = $info["public"];
	
		$query = "
		SELECT C.id as card, S.name as section, C.question, C.type
		FROM section S, card C
		WHERE deck_id = :deck AND user= :id	AND S.card_id = C.id
		order by C.id";


		// ottengo le informazioni sul mazzo
		$q2 = $pdo->prepare($query);
		$q2->bindParam(':id', $_SESSION["session_mail"], PDO::PARAM_STR);
		$q2->bindParam(':deck', $_GET["id"], PDO::PARAM_STR);
		$q2->execute();
		$result =  $q2->fetchAll(PDO::FETCH_ASSOC);

		/*$query = "
		SELECT A.card_id as card, A.id as id, A.answer
		FROM Answers
		WHERE deck_id = :deck AND user= :id	AND S.card_id = C.id
		order by C.id";*/

		// imposto tutte le carte
		foreach($result as $row) {
			$query = "
				SELECT A.answer, A.correct
				FROM Answers A, card C
				WHERE A.card_id = C.id AND C.id = :id
				order by A.id";

			$q2 = $pdo->prepare($query);
			$q2->bindParam(':id', $row["card"], PDO::PARAM_STR);
			$q2->execute();

			$r2 =  $q2->fetchAll(PDO::FETCH_ASSOC);

			$answer = [];

			for ($i = 0; $i < count($r2); $i++){
				$answer[$i][0] = $r2[$i]["answer"];
				$answer[$i][1] = $r2[$i]["correct"];
			}


			$cards[$row["section"]][$row["card"]]["question"] = $row["question"];
			$cards[$row["section"]][$row["card"]]["answer"]   = $answer;
			$cards[$row["section"]][$row["card"]]["type"] = $row["type"];
		}		
	}

	$query = "
		SELECT name
		FROM school
		order by name";


		// ottengo le informazioni sul mazzo
		$q3 = $pdo->prepare($query);
		$q3->execute();
		$schools =  $q3->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- PAGE -->
<!DOCTYPE html>
<html>
	<head>
		<title>Deck edit</title>
		<meta charset="utf-8">

		<!-- style -->
		<link rel="stylesheet" href="../css/layout/deck-editor.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/animations/animation.css">
		
		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<!-- scripts -->
		<script type="text/javascript" src="../js/tabs_controller.js"></script>
		<script type="text/javascript" src="../js/deck_editor.js"></script>
		<script type="text/javascript" src="../js/alertbox.js"></script>

	</head>

	<body id="deck_editor">	
		<form id="deck_form" action="#"	onsubmit='submitHandler(<?php submitID(); ?>);return false'>

			<!-- menu sinistro -->
			<div id="deck_left">

				<div id = "menu" class="content-box">				
					<h1>Creazione del mazzo</h1>
					<label id="deck_editor_lbl_id" name="deck_id"><?php if(isset($_GET["id"])) echo "#".$_GET["id"]; ?></label>
					<h2>Inserisci le informazioni per procedere</h2>
					<div id="preview" style=<?php echo '"background-color:'.$color.';"'; ?> ><label id="lbl_preview"><?php echo $name;?></label></div>

					<label id="deck_editor_lbl_name"  >Nome*:</label>
					<label id="deck_editor_lbl_school">Università/Scuola:</label>
					<label id="deck_editor_lbl_degree">Corso:</label>

					<label id="deck_editor_lbl_public">Public:</label>
					<label class="switch">
						<?php 
							if($public == 1)
								echo "<input type='checkbox' name='public' checked value='TRUE'>";
							else
								echo "<input type='checkbox' name='public' value='TRUE'>";
						?>
						<span class="slider round"></span>
					</label>

					<label id="lbl_color" >Colore:</label>

					<input type="color" id="color_picker" name="color" value=<?php echo '"'.$color.'"';?> oninput="colorChange(this)">

					<!-- Rounded switch -->
					


					<input type="text" id="txt_name" 	name="name" value=<?php echo '"'.$name.'"'	?> placeholder="Nome del mazzo" onchange='deckNameChange(this)' oninput='deckNameInput(this)' required oninvalid='onInvalidText()' autocomplete="off">
					
					<select name="school" id="txt_school">
						<option value="NULL">Nessuna</option>
						<?php
							foreach($schools as $s){
								$selected = "";
								if($s['name'] == $school) $selected='selected="selected"';

								echo "<option value='{$s['name']}' {$selected}>{$s['name']}</option>";
							}
						?>
					</select>
					
					<input type="text" id="txt_degree"  value=<?php echo '"'.$degree.'"'?> placeholder="Corso di riferimento">
				</div>

				<div id="left-bottom">
					<label id="error_msg"></label>
					<button type="button" id="delete-deck" class="btn-remove" onclick='deleteDeck(<?php submitID(); ?>)'></button>
					<button type="button" id="back" value="Indietro" onclick="goBack()">Indietro</button>
					<input type="submit" class="submit" id="deck_editor_submit" value="Salva">
				</div>
			</div>

			
			<!-- menu destro  window.location.href='./dashboard.php'   -->
			<div id="deck_right">
				<div class="tab-header" id="tab-header-0">
					<?php
						if (isset($cards)){loadTabs($cards);}
						else { defaultTabs();}

						echo "<button type='button' class='add-tab'></button>";
					?>
				</div>

				<!-- posizione in cui vengono caricate le tabs -->
				<div class="content-box" id="tab-content-0">
					<?php
						if (isset($cards)){
								loadCards($cards);
						}

						else {
							defaultTabContainer();
						}
					?>
				</div>
			</div>
		</form>

		<!-- Footer -->
		<?php require("../html/footer.php");?>
	</body>
</html>

