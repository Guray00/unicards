<?php
	require("../php/utils.php");
	require_once("../php/database.php");

	$match_id = $_GET["id"];

	function createUser($username, $position, $user, $correct, $wrong, $skip){


		$total = $correct + $wrong + $skip;
	
		$sc = ($correct/$total)*100;
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
		
		select sum(corrette) as corrette, sum(wrong) as wrong, sum(skip) as skip, user, U.username from 
		
		(	

			-- conta le inserite
			(

				select 1 as corrette, 0 as wrong, 0 as skip, D.user from (

					SELECT count(DISTINCT A.id) as inserite, (
						
						-- conto il totale delle giuste
						select count(DISTINCT id) as corrette
						from answers AA
						where 
						AA.card_id = A.card_id and
						AA.correct = 1
						
					) as corrette, P.user

					from points P, answers A
					where
						P.card_id = A.card_id and A.correct = 1 and P.match_id = :match_1 and P.answer_id is not null and P.answer_id = A.id 				
					group by P.user, A.card_id
				
				) as D

				where D.inserite = D.corrette
			)

			UNION ALL

			-- trova tutte le risposte segnate sbagliate
			(
				select 0 as corrette, 1 as wrong, 0 as skip, P.user
				from points P, answers A
				where
					P.card_id = A.card_id AND
					P.match_id = :match_2 AND
					(  (P.answer_id IS NULL) OR (P.answer_id IS NOT NULL AND P.answer_id = A.id AND A.correct = 0)) 

					
					GROUP BY P.user, P.card_id	
			) 

			-- calcola le domande skippate
			UNION ALL 
			(
				Select 0 as corrette, 0 as wrong, count(distinct c.card_id) as skip, PP.user 
				from section C, card CC, `match` M, player PP
				Where 
				C.deck_id = m.deck_id AND
				C.user = m.owner AND
				PP.match_id = M.id AND
				M.id = :match_3 and
				C.card_id = CC.id and
				M.mode <= CC.type
				
				And C.card_id not in 
					(
						Select P.card_id 
						from points P
						Where 
							P.match_id = :match_4 -- and P.user = PP.user
					)

				Group by PP.user
			)

		) as D, User U where U.mail = D.user group by user order by corrette DESC;";
					
		$q1 = $pdo->prepare($query);
		$q1->bindParam(':match_1', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_2', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_3', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_4', $match_id, PDO::PARAM_STR);
		$q1->execute();
		$result =  $q1->fetchAll(PDO::FETCH_ASSOC);


		$username = "";
		

		foreach($result as $i => $x){
			if (count($result) > 1) $username = $x["username"];
			createUser($username, $i, $x["user"], $x["corrette"], $x["wrong"], $x["skip"]);
		}

		//var_dump($result);
	}

	/* INIZIO SVILUPPO */

?>

<!DOCTYPE html>
<html lang="it">
	<head>
		<title>Unicards Ranking</title>
		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="../assets/favicon.png"/>

		<link rel="stylesheet" href="../css/layout/2-column-layout.css">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/animations/animation.css">
		<link rel="stylesheet" href="../css/layout/ranking.css">

		<script src="../js/alertbox.js"></script>
		<script src="../js/ranking.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>
	
	<body>

		<div class="back-circle-absolute" onclick="window.location.href='./dashboard.php'"></div>

		<div class="container">	
			<h1 id="title">CLASSIFICA</h1>
			<?php loadUsers();?>			
		</div>

	</body>
</html>