<?php
	require("../php/utils.php");
	require_once("../php/database.php");


	$match_id = $_GET["id"];



	
	

	function createUser($username, $position, $user, $total, $wrong, $skip){

		//$total = $correct + $wrong + $skip;
		//echo $wrong;


	
		$sc = (($total - $wrong - $skip)/$total)*100;
		$sw = ($wrong/$total)*100;
		$ss = ($skip/$total)*100;


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
		
		select sum(wrong) as wrong, sum(skip) as skip, user from 
		
		(	

			-- trova tutte le risposte segnate sbagliate
			(
				select 1 as wrong, 0 as skip, P.user
				from points P, answers A
				where
					P.card_id = A.card_id AND
					P.match_id = :match_2 AND
					(  (P.answer_id IS NULL) OR (P.answer_id IS NOT NULL AND P.answer_id = A.id AND A.correct = 0)) 

					
					GROUP BY P.user, P.card_id	
			) 

			-- calcola le domande skippate
			UNION ALL (
				Select 0 as wrong, count(distinct c.card_id) as skip, PP.user 
				from section C, `match` M, player PP
				Where 
				C.deck_id = m.deck_id AND
				PP.match_id = M.id AND
				M.id = :match_3
				
				And C.card_id not in 
					(
						Select P.card_id 
						from points P
						Where 
							P.match_id = :match_4
					)

				Group by PP.user
			)

		) as D;";
					
		$q1 = $pdo->prepare($query);
		$q1->bindParam(':match_2', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_3', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_4', $match_id, PDO::PARAM_STR);
		$q1->execute();
		$result =  $q1->fetchAll(PDO::FETCH_ASSOC);


		$query = "(
					select count(DISTINCT S.card_id) as total
					from section S, `match` M
					WHERE S.deck_id = M.deck_id and M.id = :match
				);";

		$q1 = $pdo->prepare($query);
		$q1->bindParam(':match', $match_id, PDO::PARAM_STR);
		$q1->execute();
		$total =  $q1->fetch()["total"];


		$username = "";
		if (count($result) > 1) $username = $_SESSION["session_username"];

		foreach($result as $i => $x){
			createUser($username, $i, $x["user"], $total, $x["wrong"], $x["skip"]);
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