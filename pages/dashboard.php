<!DOCTYPE html>
<html>
	<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		_sessionCheck();

		require_once("../php/database.php");
		

		// imposta la pagina corrente per la navbar
		$currentpage = "dashboard";


		$query = "
		SELECT *
		FROM deck
		where user= :user order by id";


		// ottengo le informazioni sul mazzo
		$request = $pdo->prepare($query);
		$request->bindParam(':user', $_SESSION["session_mail"], PDO::PARAM_STR);
		$request->execute();
		$deck_list =  $request->fetchAll(PDO::FETCH_ASSOC);

	?>

	<head>
		<title>Dashboard</title>
		<meta charset="utf-8">
		
		<!--  Stili  -->
		<link rel="stylesheet" href="../css/layout/dashboard.css">
		<link rel="stylesheet" href="../css/layout/1-column-layout.css">
		<link rel="stylesheet" href="../css/theme.css">
	</head>

	<!-- griglia -->
	<body class="container-side-right">
	
		<!-- Navbar -->
		<?php require("../html/nav.php");?>

		<!-- Menu sinistro -->
		<div class="left content-box">	
			<h1>I tuoi mazzi</h1>
			<div id="deck_container">
				<?php 
					for ($i = 0;  $i < count($deck_list); $i++) {
						//echo "<a class='preview_link' href='./deck_editor.php?id={$deck_list[$i]["id"]}'><div class='dashboard-deck-preview'	style='background-color:{$deck_list[$i]["color"]};'>{$deck_list[$i]["name"]}</div></a>";
						echo "	
								<div class='dashboard-deck-preview'	style='background-color:{$deck_list[$i]["color"]};'>
									<a class='preview_link' href='#'>{$deck_list[$i]["name"]}</a>
									<button class='btn-edit' onclick='window.location.href=\"./deck_editor.php?id={$deck_list[$i]["id"]}\"';></button>
								</div>		
							";
					}
				?>
				<a class='preview_link' href='./deck_editor.php'><div class="add"></div></a>
				
			</div>
		</div>

		<!--  Menu destro  -->
		<div  class="right side-box">
			<p><?php echo "Nome utente: ".$_SESSION["session_username"]?></p>
		</div>

		<!--  Footer  -->
		<?php require("../html/footer.php");?>						
	</body>
</html>