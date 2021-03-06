<!DOCTYPE html>
<html lang="it">
	<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		_sessionCheck();

		require_once("../php/database.php");
		

		// imposta la pagina corrente per la navbar
		$currentpage = "dashboard";


		$query = "
		SELECT D.id, D.user, D.name, D.color, D.public, F.owner as favourite	
		from (	select id, user, name, color, public
				FROM deck
				where user= :user order by id) as D
		LEFT OUTER JOIN favourite F on D.user = F.user and D.id = F.deck and D.user = F.owner
		";

		// ottengo le informazioni sul mazzo
		$request = $pdo->prepare($query);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->execute();
		$deck_list =  $request->fetchAll(PDO::FETCH_ASSOC);


		$query = "
			SELECT D.name, D.id, D.color, D.public, D.user 
			from favourite F, Deck D where 
				F.user = :user  and 
				D.user = F.owner and
				F.user = D.user and 
				D.id = F.deck
			
			UNION
			
			SELECT D.name, D.id, D.color, D.public, D.user 
			from favourite F, Deck D where 
				F.user = :user2 and 
				D.user = F.owner and 
				F.user <> D.user and
				D.id = F.deck  and 
				D.public is TRUE
		";

		// ottengo le informazioni sul mazzo
		$request = $pdo->prepare($query);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->bindParam(':user2', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->execute();
		$favourite_list =  $request->fetchAll(PDO::FETCH_ASSOC);


		$query = "
			select count(DISTINCT match_id) as total from `points` P, `match` M
			where 
				P.match_id = M.id and
				M.status is not null and
				M.status != 0 and
				P.user = :user

		";

		// ottengo le informazioni sul mazzo
		$request = $pdo->prepare($query);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->execute();
		$partite =  $request->fetch()["total"];


		function make_deck_preview($deck, $favourite_id="preview_list_normal"){
			$favourite = ($deck["favourite"] == NULL) ? "" : "checked";
			$public    = ($deck["public"   ] == 1) ? "btn-lock-open" : "btn-lock-close";

			echo "	
					<div class='dashboard-deck-preview'	style='background-color:{$deck["color"]};'>
						
						<label class='{$public}'></label>
						<a class='preview_link' href='#' onclick='openMenu(\"{$deck["id"]}\", \"{$deck["user"]}\", \"{$deck["name"]}\", \"{$deck["color"]}\", \"{$_SESSION["session_mail"]}\");' >{$deck["name"]}</a>
						<input type='checkbox' class='btn-favourite' id='{$favourite_id}{$deck["id"]}' value='{$deck["id"]}' onchange='setFavourite(this, \"{$_SESSION["session_mail"]}\")' {$favourite}>
						<label class='btn-favourite' for='{$favourite_id}{$deck["id"]}'></label>";
							
			if ($_SESSION["session_mail"] == $deck["user"])
				echo "<button class='btn-edit' onclick='window.location.href=\"./deck_editor.php?id={$deck["id"]}\"'></button>";
			echo "</div>";
		}


		$img = "../assets/users/".$_SESSION["session_mail"];
	?>

	<head>
		<title>Dashboard</title>
		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="../assets/favicon.png"/>


		
		<!--  Stili  -->
		<link rel="stylesheet" href="../css/layout/dashboard.css">
		<link rel="stylesheet" href="../css/layout/1-column-layout.css">
		<link rel="stylesheet" href="../css/theme.css">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="../js/dashboard.js"></script>
		<script src="../js/alertbox.js"></script>
	</head>

	<!-- griglia -->
	<body class="container-side-right">
	
		<!-- Navbar -->
		<?php require("../html/nav.php");?>

		<div id="choose-mod" class="content-box">

			<button id="close-menu" onclick="closeMenu()">???</button>

			<div id="selector">
					<div>
						<div id="menu-deck-preview">
						</div>

						<button id="edit-btn">Modifica</button>
					</div>
					

				<div id="game-mod-selector">
					<button id="sp">Singolo giocatore</button>
					<button id="mp">Multigiocatore</button>
				</div>

			</div>
		</div>

		<div id="opacity"></div>

		<!-- Menu sinistro -->
		<div class="left content-box">	

			<div id="favourites-list">

				<?php 

					if (count($favourite_list) > 0){
						$n = count($favourite_list);

						echo "<h1>Preferiti ({$n})</h1>
								<div class='deck_container'>";

								for ($i = 0;  $i < count($favourite_list); $i++) {
									$favourite_list[$i]["favourite"] = "checked";
									make_deck_preview($favourite_list[$i], "deck_favourite_list");
								}

						echo "</div>";
					}
				?>
				
			</div>

			<div id="your-deck-list">
				<h1>I tuoi mazzi (<?php echo count($deck_list);?>)</h1>
				<div class="deck_container">
					<?php 
						for ($i = 0;  $i < count($deck_list); $i++) {
							make_deck_preview($deck_list[$i]);	
						}
					?>

					<a class='preview_link' href='./deck_editor.php'><div class="add"></div></a>
					
				</div>
			</div>
			
			
		</div>

		<!--  Menu destro  -->
		<div  class="right side-box">
			<div class="img-container">
				<img src='<?php global $img; echo $img?>' onerror="this.src='../assets/users/default_black.svg'" alt="Avatar" />
				<input name="upload" value="" id="fake-url">
				<input type="file" id="upload" class=".img-chooser" accept="image/*">
			</div>
			
			<p><b>Nome utente:</b> <?php echo $_SESSION["session_username"]?></p>
			<p><b>Mail:</b> <?php echo $_SESSION["session_mail"]?></p><br>
			<p>Hai partecipato a un totale di <b><?php global $partite; echo $partite; ?></b> partite</p>
		</div>

		<!--  Footer  -->
		<?php require("../html/footer.php");?>						
	</body>
</html>