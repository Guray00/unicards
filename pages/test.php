
<?php

$pass = strtoupper(substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 4));
echo $pass;

?>

<html>
<head>
	<DOCTYPE html>

	<link rel="stylesheet" href="../css/elements/alertbox.css">
	<link rel="stylesheet" href="../css/layout/1-column-layout.css">
	<link rel="stylesheet" href="../css/theme.css">
</head>


<body>
	<div class="content-box alertbox">
		<button class="close">✕</button>
		<h1>Suspendisse potenti. Pellentesque commodo</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ac tristique odio, vel placerat elit. Duis vulputate felis risus, sed imperdiet turpis malesuada vitae. Nullam efficitur a sapien eu scelerisque. </p>
		<div class='buttons-container'>
								<button>Si</button>
								<button>No</button>
							</div>
	</div>
</body>

</html>