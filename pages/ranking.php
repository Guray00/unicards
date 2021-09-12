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

					<div class='progressbar skip style='width: {$sw}%;'>
					</div>

					<div class='progressbar false' style='width: {$ss}%;'>
					</div>
				</div>
			</div>";

			


	}


	function loadUsers(){

		global $match_id, $pdo;


		// conto quante sono quelle esatte
		$query=" 	select sum(correct) as correct, sum(wrong) as wrong, sum(skip) as skip, user, match_id from (
			(select count(*) as correct, 0 as wrong, 0 as skip, P.user, P.match_id
		from answers A, card C, `points` P
		where 
			C.id = A.card_id AND
			A.correct = 1 and
			P.card_id = C.id AND
			P.answer_id IS NOT NULL and
			P.match_id = :match_1
			
		GROUP BY P.match_id, P.user) 
		
		UNION

		(
			select count(*) as wrong, 0 as correct, 0 as skip, P.user, P.match_id
			from answers A, card C, `points` P
			where 
				C.id = A.card_id AND
				A.correct = 0 and
				P.card_id = C.id AND
				P.answer_id IS NOT NULL and
				P.match_id = :match_2
				
			GROUP BY P.match_id, P.user			
		) 

		UNION (

			select 0 as correct, 0 as wrong, count(*) as skip, P.user, P.match_id
				from `points` P
				where 
					P.answer_id IS NULL and
					P.match_id = :match_3
				GROUP BY P.match_id, P.user
		)

		) as D;";
					
		$q1 = $pdo->prepare($query);
		$q1->bindParam(':match_1', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_2', $match_id, PDO::PARAM_STR);
		$q1->bindParam(':match_3', $match_id, PDO::PARAM_STR);

		$q1->execute();
		$result =  $q1->fetchAll(PDO::FETCH_ASSOC);

		$username = "";
		if (count($result) > 1) $username = $_SESSION["session_username"];

		foreach($result as $i => $x){
			createUser($username, $i, $x["user"], $x["correct"], $x["wrong"], $x["skip"]);
		}
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
			<h1>CLASSIFICA</h1>

			<?php loadUsers();?>
			
		</div>
	</body>
</html>