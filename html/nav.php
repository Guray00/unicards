

<?php
	// controlla se la pagina è quella selezionata
	function check($currentpage, $address){
		if ($currentpage === $address)
			echo 'class="selected"';
	
		else 
			echo 'class="unselected"';	
	}
?>


<nav>
	<p>UNICARDS</p>
	<a <?php check($currentpage, "home")     ?>											>Home</a>
	<a <?php check($currentpage, "dashboard")?>	href="../pages/dashboard.php" 			>Dashboard</a>
	<a <?php check($currentpage, "about us" )?>											>Chi siamo</è>
	<a class="unselected" href="../php/logout.php" style="color:red;font-weight: bold; margin-right:-15pt; margin-left:15pt;"	>Logout</a>		
</nav>
	