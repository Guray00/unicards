<!DOCTYPE html>
<html>
	<?php
		// controlla se l'utente ha effettuato l'accesso
		require("../php/utils.php");
		_sessionCheck();

		// imposta la pagina corrente per la navbar
		$currentpage = "dashboard";
	?>

	<head>
		<title>Dashboard</title>
		<meta charset="utf-8">
		
		<!--  Stili  -->
		<link rel="stylesheet" href="../css/dashboard.css">
		<link rel="stylesheet" href="../css/theme.css">
	</head>

	<!-- griglia -->
	<body class="container">
	
		<!-- Navbar -->
		<?php require("../html/nav.php");?>

		<!-- Menu sinistro -->
		<div class="deck_show">	
			<div id="add"></div>
		</div>

		<!--  Menu destro  -->
		<div  class="menu">
			<p><?php echo $_SESSION["session_username"]?></p>
		</div>

		<!--  Footer  -->
		<?php require("../html/footer.php");?>
						
	</body>
</html>