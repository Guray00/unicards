<?php 

	require("../php/utils.php");
	_sessionCheck();
	require_once("../php/database.php");

	$match = $_GET["id"];
	$user  = $_SESSION["session_mail"];


	// recupero le informazioni essenziali del mazzo
	$query= "select * from `match` where id= :id";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$info = $request->fetch();

	// imposto le variabili per poter far avviare la partita
	$_SESSION["match_deck_id"] 		= $info["deck_id"];
	$_SESSION["match_deck_owner"] 	= $info["owner"];
	$_SESSION["match_mode"] 		= "1";
	$_SESSION["match_id"] 			= $match;
	$_SESSION["match_logged"] 		= false;


	// inserisco nel database il giocatore
	$query= "replace into `player` values (:match, :user)";
	$request = $pdo->prepare($query);
	$request->bindParam(':match', $match, PDO::PARAM_STR);
	$request->bindParam(':user', $user, PDO::PARAM_STR);
	$request->execute();


	// recupero il master della partita
	$query= "select `master` from `match` where `id` = :id";
	$request = $pdo->prepare($query);
	$request->bindParam(':id', $match, PDO::PARAM_STR);
	$request->execute();
	$master = $request->fetch();

	if(isset($master) && isset($master["master"])) $master = $master["master"];
	else {
		header("HTTP/1.1 400");
	}



	function getPath(){
		// tratto da: https://stackoverflow.com/questions/6768793/get-the-full-url-in-php
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$actual_link = substr($actual_link, 0, strpos($actual_link, "?"));
		echo $actual_link;
	}

	function getId(){
		echo strtoupper($_GET["id"]);
	}


	function showButton(){
		global $master;

		if ($master == $_SESSION["session_mail"]){
			echo "<button class='start' id='start' onclick='startMatch()'> Start </button>";
		}

		else {
			echo "<div class='warning' id='start'> In attesa che il master avvi la partita </div>";
		}
	}

	function createUsers(){

		global $pdo, $match, $master;

		$query= "select S.mail, S.username from `player` P, user S where `match_id` = :id and P.user = S.mail";
		$request = $pdo->prepare($query);
		$request->bindParam(':id', $match, PDO::PARAM_STR);
		$request->execute();
		$users = $request->fetchAll(PDO::FETCH_ASSOC);


		

		foreach ($users as $s){

			$isMaster = $s["mail"] == $master ? "master" : "";
			$isPlayer = $s["mail"] == $_SESSION["session_mail"] ? "player" : "";

			$img = "../assets/users/".$s["mail"].".png";

			echo "<div class = 'user content-box {$isMaster} {$isPlayer}' id='{$s["mail"]}'>
					<img src='{$img}'onerror=\"this.src='../assets/users/default.svg'\" />
					<p>{$s["username"]}</p>
				</div>";
		}


	}

?>


<html>

	<head>
	<title>Unicards</title>
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">
		<link rel="stylesheet" href="../css/animations/animation.css">
		<link rel="stylesheet" href="../css/layout/lobby.css">
		<link rel="stylesheet" href="../css/elements/alertbox.css">

		<script type="text/javascript" src="../js/alertbox.js"></script>
		<script type="text/javascript" src="../js/lobby.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>


	<body id="<?php echo $_GET["id"];?>">
		<div class="back-circle-absolute" onclick="window.location.href='./dashboard.php'"></div>

		<div class="title">LOBBY #<?php getId();?></div>
		<h2>Per aggiungere altri giocatori alla lobby invia il link che segue:</h2>
		<h3><div id="text-url" onclick="copyUrl();"><?php getPath();?>?id=<?php echo getId();?></div> <button id="copy" onclick="copyUrl();">Copia</button></h3>
		

		
		<?php 
			createUsers();
			showButton();
		?>


	</body>

</html>