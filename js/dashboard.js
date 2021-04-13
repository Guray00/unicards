
function setFavourite(el, owner){
	let deck_id = el.value;

	if (el.checked){
		$.ajax({
			type: "POST",
			url: "../php/add_favourite.php",
			data: {deck: deck_id, owner:owner},
	
			// in caso di successo
			success: function (data) {	
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {		
				alert(xhr.status);
				alert(thrownError);
			}
		})
	}

	else {
		$.ajax({
			type: "POST",
			url: "../php/remove_favourite.php",
			data: {deck: deck_id, owner:owner},
	
			// in caso di successo
			success: function (data) {	
			},
	
			// in caso di errore
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		})
	}

	// evitiamo un aggiornamento completo della pagina, carichiamo soltanto le carte
	$('#favourites-list').load("../pages/dashboard.php"  + ' #favourites-list');
	$('#your-deck-list') .load("../pages/dashboard.php"  + ' #your-deck-list');
}