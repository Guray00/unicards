<?php 
	require("utils.php");

	// https://stackoverflow.com/questions/15153776/convert-base64-string-to-an-image-file
	function base64_to_jpeg($base64_string, $output_file) {
    
		// apre in lettura il file
		$ifp = fopen( $output_file, 'wb' ); 

		// divide il contenuto della stringa per eliminare il tag
		$data = explode( ',', $base64_string );

		// scrive nel file
		fwrite( $ifp, base64_decode( $data[ 1 ] ) );

		// chiude il file
		fclose( $ifp ); 

		// restituisce il file
		return $output_file; 

	}

	if(isset($_SESSION["session_mail"]) && isset($_POST["upload"])){
		base64_to_jpeg($_POST["upload"], "../assets/users/".$_SESSION["session_mail"]);
	}

?>