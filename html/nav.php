

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
	<a  href="../pages/landing.html" <?php check($currentpage, "home")     ?>											>Home</a>
	<a <?php check($currentpage, "dashboard")?>	href="../pages/dashboard.php" 			>Dashboard</a>
	<a target="_blank" href="https://github.com/guray00/unicards" <?php check($currentpage, "about us" ) ?>											>GitHub</a>
	<a class="unselected" href="../php/logout.php" style="color:red;font-weight: bold; margin-right:-15pt; margin-left:15pt;"	>Logout</a>		
</nav>
	