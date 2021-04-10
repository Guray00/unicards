<?php
	// controlla se l'utente ha effettuato l'accesso
	require("../php/utils.php");
	require_once("../php/database.php");

	_sessionCheck();

	$currentpage="deck_edit";

	/******************************
	 			FUNZIONI
	 *****************************/

	function submitID(){
		if(isset($_GET["id"])){
			echo $_GET["id"] . ", \"" . $_SESSION["session_mail"] . "\"";
		}

		else 
			echo "\"NULL\"" . ", \"" . $_SESSION["session_mail"] . "\"";
	}

	function defaultTabContainer(){
		echo " <div class=\"tab-container\" id='tab1'> 
					<div class='cards-container'>
						<label id='1_lbl_section'>Nome sezione:</label>
						<input class='section_name' id='1_section_name' type='text' value='Untitled' required oninvalid='onInvalidText()'></input>
						<label id='1_lbl_question1'>Domanda 1</label>
						<label id='1_lbl_answer1'>Risposta 1</label>
						<textarea id='1_question1' name='1_question1' form='deck_form' required oninvalid='onInvalidText()'></textarea>
						<textarea id='1_answer1' name='1_answer1'   form='deck_form' required oninvalid='onInvalidText()'></textarea>
						<button type='button' id='btn-add' onclick='addCard(this)'>Aggiungi carta</button> 
					</div>
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
					<label id='1_lbl_section'>Nome sezione:</label>
					<input class='section_name' id='{$tab_number}_section_name' type='text' value='{$section}' required oninvalid='onInvalidText()'></input>
					";

						$question_number = 1;
						foreach($value as $c){
							$lbl_question = $tab_number.'_lbl_question'.$question_number;
							$lbl_answer   = $tab_number.'_lbl_answer'.$question_number;
							$question = $tab_number.'_question'.$question_number;
							$answer   = $tab_number.'_answer'.$question_number;
							echo 	"	
										<label 	  id='{$lbl_question}'>Domanda {$question_number}</label>
										<label 	  id='{$lbl_answer}'  >Risposta {$question_number}</label>

										<textarea id='{$question}' name='{$question}' form='deck_form' required oninvalid='onInvalidText()'>{$c['question']}</textarea>
										<textarea id='{$answer}'   name='{$answer}' form='deck_form' required oninvalid='onInvalidText()'>{$c['answer'  ]}</textarea>";
							$question_number++;
						}
						
						echo '<button type="button" id="btn-add" onclick="addCard(this)">Aggiungi carta</button>';

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
		SELECT C.id as card, S.name as section, C.answer, C.question
		FROM section S, card C
		WHERE deck_id = :deck AND user= :id	AND S.card_id = C.id
		order by C.id";


		// ottengo le informazioni sul mazzo
		$q2 = $pdo->prepare($query);
		$q2->bindParam(':id', $_SESSION["session_mail"], PDO::PARAM_STR);
		$q2->bindParam(':deck', $_GET["id"], PDO::PARAM_STR);
		$q2->execute();
		$result =  $q2->fetchAll(PDO::FETCH_ASSOC);

		// imposto tutte le carte
		foreach($result as $row) {
			$cards[$row["section"]][$row["card"]]["question"] = $row["question"];
			$cards[$row["section"]][$row["card"]]["answer"]   = $row["answer"];
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


<!DOCTYPE html>
<html>
	<head>
		<title>Deck edit</title>

		<!-- style -->
		<link rel="stylesheet" href="../css/layout/deck-editor.css">
		<link rel="stylesheet" href="../css/theme.css">

		<!-- Jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<!-- scripts -->
		<script type="text/javascript" src="../js/tabs_controller.js"></script>
		<script type="text/javascript" src="../js/deck_editor.js"></script>
	</head>

	<body id="deck_editor">	
		<form id="deck_form" action="#"	onsubmit='submitHandler(<?php submitID(); ?>);return false'>

			<!-- menu sinistro -->
			<div id="deck_left">

				<div id = "menu" class="content-box">				
					<h1>Creazione del mazzo</h1>
					<label id="lbl_id" name="deck_id"><?php if(isset($_GET["id"])) echo "#".$_GET["id"]; ?></label>
					<h2>Inserisci le informazioni per procedere</h2>
					<div id="preview" style=<?php echo '"background-color:'.$color.';"'; ?> ><label id="lbl_preview"><?php echo $name;?></label></div>

					<label id="lbl_name"  >Nome*:</label>
					<label id="lbl_school">Università/Scuola:</label>
					<label id="lbl_degree">Corso:</label>

					<label id="lbl_public">Public:</label>
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

					<input type="color" id="color_picker" name="color" value=<?php echo '"'.$color.'"';?> onchange="colorChange(this)">

					<!-- Rounded switch -->
					


					<input type="text" id="txt_name" 	name="name" value=<?php echo '"'.$name.'"'	?> placeholder="Nome del mazzo" onchange='deckNameChange(this)' required oninvalid='onInvalidText()'>
					
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
					<button type="button" id="back" value="Indietro"><a href="./dashboard.php">Indietro</a></button>
					<input type="submit" id="submit" value="Salva">
				</div>
			</div>

			
			<!-- menu destro -->
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
		<?php require("../html/footer.php");?>
	</body>
</html>

