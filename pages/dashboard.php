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
		<link rel="stylesheet" href="../css/layout/1-column-layout.css">
		<link rel="stylesheet" href="../css/theme.css">
	</head>

	<!-- griglia -->
	<body class="container-side-right">
	
		<!-- Navbar -->
		<?php require("../html/nav.php");?>

		<!-- Menu sinistro -->
		<div class="left content-box">	
			<div class="add"></div>
			<a href="./deck_editor.php?id=1">vai</a>
		</div>

		<!--  Menu destro  -->
		<div  class="right side-box">
			<p><?php echo $_SESSION["session_username"]?></p>
		</div>

		<!--  Footer  -->
		<?php require("../html/footer.php");?>						
	</body>
</html>