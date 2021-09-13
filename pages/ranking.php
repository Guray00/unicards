<?php
	require("../php/utils.php");
	require_once("../php/database.php");


	$match_id = $_GET["id"];



	
	

	function createUser($username, $position, $user, $correct, $wrong, $total){

		//$total = $correct + $wrong + $skip;
		echo $total;


	
		$sc = ($correct/$total)*100;
		$sw = ($wrong/$total)*100;
		$ss = (($total-$correct-$wrong)/$total)*100;


		$size = 12;

		for ($i = 0; $i < $position; $i++){
			$size*=5/6;
		}

		echo "<div class='user' id='{$user}' style='height: {$size}vh;'>
				<p>{$username}</p>
		
				<div class='progress-container'>
					<div class='progressbar true'  style='width: {$sc}%;'>
					</div>

					<div class='progressbar skip' style='width: {$ss}%;'>
					</div>

					<div class='progressbar false' style='width: {$sw}%;'>
					</div>
				</div>
			</div>";

			


	}


	function loadUsers(){

		global $match_id, $pdo;


		// conto quante sono quelle esatte
		$query=" 	
		
		select sum(correct) as correct, sum(wrong) as wrong, sum(total) as total, user, match_id from 
		
		(			
			-- trova tutte le risposte segnate sbagliate
			(
				select 0 as correct, 1 as wrong, 0 as total, P.user, P.match_id
				from answers A, card C, `points` P
				where 
				C.id = A.card_id AND
				P.card_id = C.id AND
				(	
					(P.answer_id IS NOT NULL and P.answer_id = A.id AND A.correct = 0) or 
					(P.answer_id is null)) AND 
				
				P.match_id = :match_2
					
				GROUP BY P.match_id, P.user, C.id		
			) 

			-- calcola il totale delle domande
			UNION (
				select 0 as correct, 0 as wrong, COUNT(DISTINCT C.id) as total, P.match_id, P.user
				from card C, `match` M, section S, player P
				where S.card_id = C.id AND
				M.deck_id = S.deck_id AND
				M.id = P.match_id and
				M.id=:match_3
				GROUP BY P.match_id, P.user
			)

		) as D;";
					
		$q1 = $pdo->prepare($query);
		$q1->bindParam(':match_1', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_2', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_3', $match_id, PDO::PARAM_STR);
		//$q1->bindParam(':match_4', $match_id, PDO::PARAM_STR);

		$q1->execute();
		$result =  $q1->fetchAll(PDO::FETCH_ASSOC);

		$username = "";
		if (count($result) > 1) $username = $_SESSION["session_username"];

		foreach($result as $i => $x){
			createUser($username, $i, $x["user"], $x["correct"], $x["wrong"], $x["total"]);
		}

		//var_dump($result);
	}

	/* INIZIO SVILUPPO */

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Unicards</title>
		<link rel="stylesheet" href="../css/layout/2-column-layout.css">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/animations/animation.css">
		<link rel="stylesheet" href="../css/layout/ranking.css">

		<script type="text/javascript" src="../js/alertbox.js"></script>
		<script type="text/javascript" src="../js/ranking.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>
	
	<body>

		<div class="container">	
			<h1 id="title">CLASSIFICA</h1>

			<?php loadUsers();?>
			
		</div>
	</body>
</html>