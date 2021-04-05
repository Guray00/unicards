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
		<link rel="stylesheet" href="../css/dashboard.css">
		<link rel="stylesheet" href="../css/design.css">
		<link rel="stylesheet" href="../css/theme.css">

	</head>

	<body>

			
			
			<div class="container">
				<?php require("../html/nav.php");?>

				<div class="deck_show">	
				</div>
				<div  class="menu">
					<p><?php echo $_SESSION["session_username"]?></p>
				</div>
			</div>
			
			<?php require("../html/footer.php");?>
	</body>
</html>